<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Detalle;
use App\Models\Factura;
use App\Models\Movimiento;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use App\Utils\Respuesta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FacturaController extends Controller
{
    public function formulario(Request $request)
    {

        $usuario    = Auth::user();
        $sucursal   = $usuario->sucursal;

        $productos = Producto::select('productos.id', 'productos.codigo', 'productos.nombre', 'productos.precio_venta', DB::raw("'PRODUCTO' as tipo"), 'productos.control_stock')
            ->join('movimientos', 'movimientos.producto_id', '=', 'productos.id')
            ->selectRaw('SUM(movimientos.ingreso) - SUM(movimientos.salida) as stock')
            ->where('movimientos.sucursal_id', $sucursal->id)
            ->where('productos.control_stock', true)
            ->where('productos.tipo', 'PRODUCTO')
            ->whereNull('movimientos.deleted_at')
            ->groupBy('productos.id', 'productos.nombre');

        // $masServicios = Producto::select('id', 'codigo', 'nombre', 'precio_venta', DB::raw("'SERVICIO' as tipo"), 'control_stock', DB::raw('100 as stock'))
        //     ->where('control_stock', false);

        // $servicios = $productos->union($masServicios)->get();
        $servicios = $productos->get();

        // PARA VERIFICAR LA CAJA
        $caja = new Caja();
        $cajaAbierta = $caja->sacaCajaVigente($sucursal->id);

        return view('factura.formulario')->with(compact('servicios', 'cajaAbierta', 'usuario'));
    }

    public function ajaxListadoClientesBusqueda(Request $request)
    {
        if ($request->ajax()) {

            $query = Cliente::select('*');

            if (!is_null($request->input('nit_escogido'))) {
                $nit = $request->input('nit_escogido');
                $query->where('nit', $nit)
                    ->orWhere('cedula', $nit);
            }

            if (!is_null($request->input('nombre_escogido'))) {
                $nombre = $request->input('nombre_escogido');
                $query->where('nombres', 'LIKE', "%$nombre%");
            }

            if (!is_null($request->input('ap_paterno_escogido'))) {
                $paterno = $request->input('ap_paterno_escogido');
                $query->where('ap_paterno', 'LIKE', "%$paterno%");
            }

            if (!is_null($request->input('ap_materno_escogido'))) {
                $materno = $request->input('ap_materno_escogido');
                $query->where('ap_materno', 'LIKE', "%$materno%");
            }

            if (
                !is_null($request->input('cedula_escogido')) &&
                !is_null($request->input('nombre_escogido')) &&
                !is_null($request->input('ap_paterno_escogido')) &&
                !is_null($request->input('ap_materno_escogido'))
            ) {

                $clientes = $query->limit(5)->get();
            } else {
                $clientes = $query->orderBy('id', 'desc')->limit(10)->get();
            }

            $valores = [
                'listado' => view('factura.ajaxListadoClientesBusqueda')->with(compact('clientes'))->render(),
                'cantidad' => count($clientes)
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function emitirRecibo(Request $request)
    {

        if ($request->ajax()) {
            try {

                // dd($request->all());

                $usuario             = Auth::user();
                $carroVentas         = $request->input('carrito');
                $cliente_id          = $request->input('cliente_id');
                $descuento_adicional = $request->input('descuento_adicional');
                $monto_total         = (float)$request->input('monto_total');
                $tipo_pago_pagado    = $request->input('tipo_pago_pagado');
                $monto_total_pagado  = (float)$request->input('monto_total_pagado');
                $monto_pagado        = (float)$request->input('monto_pagado');
                $descripcion         = $request->input('descripcion');

                $cliente        = Cliente::with('sucursal')->find($cliente_id);
                $sucursal_objeto     = $usuario->sucursal;
                $sucursal_id         = $sucursal_objeto->id;

                // if($descuento_adicional > 0){
                //     $totalSubTotales = 0;
                //     foreach ($carroVentas as $item)
                //         $totalSubTotales += (float) $item['subTotal'];

                //     $descuento_adicional = $totalSubTotales - $monto_total;

                // }

                // PREGUNTAMOS POR LA CAJA
                $caja = new Caja();
                $cajaAbierta = $caja->sacaCajaVigente($sucursal_id);
                if ($cajaAbierta == null) {
                    $data['estado'] = false;
                    $data['text'] = 'NO SE ENCONTRO UN CAJA ABIERTA';
                    return $data;
                }

                // //PREGUNTAMOS SI PAGO TODO O NO
                // if ($monto_final < $monto_total) {
                //     $data['estado'] = false;
                //     $data['text'] = 'DEBE CANCELAR LA TOTALIDAD DE LA VENTA';
                //     return $data;
                // } elseif ($monto_final > $monto_total) {
                //     $data['estado'] = false;
                //     $data['text'] = 'EL MONTO PROPORCIONADO EXEDE AL MONTO DE LA VENTA';
                //     return $data;
                // }

                $idDetalles = array();
                $esTransporte = false;

                //================================== COMENZAMOS LA TRANSACCION ==================================
                DB::beginTransaction();

                // ----------------- AGREGAMOS EN L ATABLA DETALLES -----------------
                foreach ($carroVentas as $key => $item) {

                    $servicio = Producto::find($item['servicio_id']);

                    $detalle                        = new Detalle();
                    $detalle->usuario_creador_id    = $usuario->id;
                    $detalle->sucursal_id           = $sucursal_id;
                    $detalle->producto_id           = $item['servicio_id'];
                    $detalle->nombre_producto       = $servicio->nombre;
                    $detalle->descripcion_adicional = $item['descripcion_adicional'];
                    $detalle->precio_compra         = $servicio->precio_compra;
                    $detalle->precio                = $item['precio'];
                    $detalle->cantidad              = $item['cantidad'];
                    $detalle->descuento             = $item['descuento'];
                    $detalle->total                 = $item['total'];
                    $detalle->importe               = $item['subTotal'];
                    $detalle->fecha                 = date('Y-m-d H:i:s');
                    $detalle->estado                = 'Pagado';
                    $detalle->save();

                    if ($servicio->control_stock == 1) {
                        //VERIFICAMOS QUE EXISTA EN ALMACEN ANTES DE CONTINUAR
                        $cantidad_almacen = $this->cantidadStockEmpresa($sucursal_id, $item['servicio_id']);

                        if ($cantidad_almacen->estado) {
                            if ($item['cantidad'] > $cantidad_almacen->data['cantidad']) {
                                DB::rollBack();
                                $data = Respuesta::error(null, 'Cantidad Solicitada ' . $item['cantidad'] . ', cantidad en almacen ' . $cantidad_almacen->data['cantidad'] . ' del producto ' . $servicio->descripcion);
                                return $data;
                            }
                        } else {
                            $data = $cantidad_almacen;
                            DB::rollBack();
                            return $data;
                        }

                        //AQUI LO MOVEREMOS LOS DETALLES PARA NO HACER OTRO FOR ABAJO
                        $movimiento                     = new Movimiento();
                        $movimiento->usuario_creador_id = $usuario->id;
                        $movimiento->sucursal_id        = $sucursal_objeto->id;
                        $movimiento->producto_id        = $servicio->id;
                        $movimiento->detalle_id         = $detalle->id;
                        $movimiento->salida             = $detalle->cantidad;
                        $movimiento->ingreso            = 0;
                        $movimiento->fecha              = date('Y-m-d H:i:s');
                        $movimiento->descripcion        = 'VENTA';
                        $movimiento->save();
                    }

                    array_push($idDetalles, $detalle->id);
                }

                // ================================== Si todo ha pasado correctamente, hacer commit ==================================
                DB::commit();

                $numeroFacturaRecibo = $this->numeroRecibo($sucursal_objeto->id);
                $numeroFacturaRecibo = ($numeroFacturaRecibo == null ? 1 : ($numeroFacturaRecibo + 1));

                // ESTO ES PARA LA FACTURA LA CREACION
                $facturaVerdad                          = new Factura();
                $facturaVerdad->usuario_creador_id      = Auth::user()->id;
                $facturaVerdad->cliente_id              = $cliente->id;
                $facturaVerdad->sucursal_id             = $sucursal_objeto->id;
                $facturaVerdad->fecha                   = date('Y-m-d H:i:s');
                $facturaVerdad->numero_recibo           = $numeroFacturaRecibo;
                $facturaVerdad->facturado               = "No";
                $facturaVerdad->total                   = $monto_total;
                $facturaVerdad->monto_total_subjeto_iva = $monto_total;
                $facturaVerdad->descuento_adicional     = $descuento_adicional;
                $facturaVerdad->estado_pago             = ($monto_pagado >= $monto_total) ? 'PAGADO' : 'DEUDA';
                $facturaVerdad->descripcion             = $descripcion;
                $facturaVerdad->save();

                // AHORA AREMOS PARA LOS DETALLES
                Detalle::whereIn('id', $idDetalles)
                    ->update([
                        'estado'     => 'Finalizado',
                        'factura_id' => $facturaVerdad->id
                    ]);

                // PARA LA TABLA PAGOS
                if ($monto_pagado > 0) {
                    $pago                     = new Pago();
                    $pago->usuario_creador_id = $usuario->id;
                    $pago->factura_id         = $facturaVerdad->id;
                    $pago->sucursal_id        = $sucursal_id;
                    $pago->caja_id            = $cajaAbierta->id;
                    $pago->monto              = ($monto_pagado >= $monto_total) ? $monto_total : $monto_pagado;
                    $pago->fecha              = $facturaVerdad->fecha;
                    $pago->descripcion        = 'VENTA';
                    $pago->apertura_caja      = 'No';
                    $pago->tipo_pago          = $tipo_pago_pagado;
                    $pago->estado             = 'INGRESO';
                    $pago->save();
                }

                $data = Respuesta::success([
                    'url_pdf' => url('factura/imprimeRecibo/' . $facturaVerdad->id),
                    'numero' => $facturaVerdad->id
                ], 'Se registro con exito el recibo!');
            } catch (\Exception $e) {
                $data = Respuesta::error(null, $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            }
        } else {
            $data = Respuesta::error(null, "No existe");
        }

        return $data;
    }

    public function numeroRecibo($sucursal_id)
    {

        $numeroRecibo = Factura::where('sucursal_id', $sucursal_id)
            ->selectRaw('MAX(CAST(numero_recibo AS UNSIGNED)) as numero_recibo')
            ->pluck('numero_recibo')
            ->first();

        return $numeroRecibo;
    }

    public function imprimeRecibo(Request $request, $factura_id)
    {

        $usuario = Auth::user();
        $factura = Factura::with('detalles.producto', 'sucursal', 'cliente')->find($factura_id);

        if ($factura) {
            $pdf = Pdf::loadView('factura.pdf.imprimeRecibo', compact('factura'))->setPaper('letter');

            return $pdf->stream('recibo.pdf');
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function imprimeReciboRollo(Request $request, $factura_id)
    {

        $usuario = Auth::user();
        $factura = Factura::with('detalles.producto', 'sucursal', 'cliente')->find($factura_id);

        if ($factura) {
            $customPaper = [0, 0, 198.43, 2000];
            $pdf = Pdf::loadView('factura.pdf.imprimeReciboRollo', compact('factura'))->setPaper($customPaper);

            return $pdf->stream('recibo.pdf');
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function listado(Request $request)
    {

        $clientes = Cliente::orderBy('nombres', 'desc')
            ->orderBy('ap_paterno', 'asc')
            ->orderBy('ap_materno', 'asc')
            ->get();

        $usuario = Auth::user();

        if (in_array($usuario->rol_id, [1, 2, 5, 6])) {
            if ($usuario->rol_id == 6) {
                $sucursales = Sucursal::where('id', $usuario->sucursal_id)->get();
            } else {
                $sucursales = Sucursal::all();
            }
        } else {
            $sucursales = Sucursal::where('id', $usuario->sucursal_id)->get();
        }

        $vendedores = User::all();


        return view('factura.listado')->with(compact('clientes', 'vendedores', 'sucursales'));
    }

    public function ajaxListadoFacturas(Request $request)
    {
        if ($request->ajax()) {

            // dd($request->all());

            $usuario    = Auth::user();
            $usuario_id = Auth::user()->id;

            // DE AQUI ESE EL ANTIGUO

            $query = Factura::select(
                'facturas.numero_cafc',
                'facturas.estado',
                'facturas.codigo_descripcion',
                'facturas.tipo_factura',
                'facturas.uso_cafc',
                'facturas.nit',
                'facturas.cuf',
                'facturas.id',
                'facturas.fecha',
                'facturas.total',
                'facturas.numero_factura',
                'facturas.usuario_creador_id',
                'facturas.facturado',
                'facturas.numero_recibo',
                'facturas.sucursal_id',
                'clientes.cedula',
                'clientes.nombres',
                'clientes.ap_paterno',
                'clientes.ap_materno',
            )
                ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id')
                ->where('facturas.facturado', 'No');

            if ($usuario->rol_id == 4) {
                $query->where('facturas.usuario_creador_id', $usuario->id);
            } else {
                if (!is_null($request->input('buscar_cliente_id'))) {
                    $cliente_id = $request->input('buscar_cliente_id');
                    $query->where('clientes.id', $cliente_id);
                }

                if (!is_null($request->input('buscar_vendedor_id'))) {
                    $vendedor_id = $request->input('buscar_vendedor_id');
                    $query->where('facturas.usuario_creador_id', $vendedor_id);
                }
            }

            if (!is_null($request->input('buscar_sucursal_id'))) {
                $sucursal_id = $request->input('buscar_sucursal_id');
                $query->where('facturas.sucursal_id', $sucursal_id);
            }

            if (!is_null($request->input('buscar_fecha_inicio')) && !is_null($request->input('buscar_fecha_fin'))) {
                $fecha_ini = $request->input('buscar_fecha_inicio');
                $fecha_fin = $request->input('buscar_fecha_fin');
                $query->whereBetween('facturas.fecha', [$fecha_ini . " 00:00:00", $fecha_fin . " 23:59:59"]);
            }

            if (
                !is_null($request->input('buscar_cliente_id')) &&
                !is_null($request->input('buscar_vendedor_id')) &&
                !is_null($request->input('buscar_fecha_inicio')) &&
                !is_null($request->input('buscar_fecha_fin'))
            ) {
                $facturas = $query->limit(500)->get();
            } else {
                $facturas = $query->orderBy('facturas.id', 'desc')->limit(100)->get();
                // $facturas = $query->orderBy('facturas.id', 'desc')->with('empresa')->get();
            }

            $url_verifica_factura = null;
            $nitEmpresa = null;

            $valores = [
                'listado' => view('factura.ajaxListadoFacturas')->with(compact('facturas', 'url_verifica_factura', 'nitEmpresa'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            // $data['text']   = 'No existe';
            // $data['estado'] = 'error';
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function anularRecibo(Request $request)
    {
        if ($request->ajax()) {

            $usuario    = Auth::user();
            $factura_id = $request->input('venta');

            $factura                        = Factura::find($factura_id);
            $factura->usuario_eliminador_id = $usuario->id;
            $factura->estado                = 'Anulado';
            $factura->save();

            $detalles = Detalle::where('factura_id', $factura_id)->get();

            $idDetalles = $detalles->pluck('id');

            Movimiento::whereIn('detalle_id', $idDetalles)
                ->update([
                    'usuario_eliminador_id' => $usuario->id
                ]);

            Movimiento::whereIn('detalle_id', $idDetalles)->delete();

            // ELIMINAMOS LOS DETALLES DE LA FACTURA
            Detalle::where('factura_id', $factura->id)
                ->update([
                    'usuario_eliminador_id' => $usuario->id
                ]);
            Detalle::where('factura_id', $factura->id)->delete();

            // ELIMINAMOS LOS PAGOS DE LA FACTURA
            Pago::where('factura_id', $factura->id)
                ->update([
                    'usuario_eliminador_id' => $usuario->id
                ]);
            Pago::where('factura_id', $factura->id)->delete();

            $data = Respuesta::success(null, "Se elimino con exito el registro.");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    // ********************* FUNCIONES PRIVADAS **************
    public function cantidadStockEmpresa($sucursal_id, $id_servicio)
    {
        $movimientoModelo = new Movimiento();
        $stock = $movimientoModelo->cantidaDisponile($sucursal_id, $id_servicio);

        if ($stock > 0) {
            $valores = [
                'cantidad' => $stock,
            ];
            $data = Respuesta::success($valores, "Cantidad Existente en almacen");
        } else {
            $data = Respuesta::error(null, "Cantidad no disponible en almacen");
        }
        return $data;
    }
}
