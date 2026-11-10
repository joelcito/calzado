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
use App\Utils\CuentaHermano;
use App\Utils\Respuesta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FacturaController extends Controller
{
    public function formulario(Request $request)
    {

        $usuario = Auth::user();
        $sucursal = $usuario->sucursal;

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
        $caja = new Caja;
        $cajaAbierta = $caja->sacaCajaVigente($sucursal->id);

        return view('factura.formulario')->with(compact('servicios', 'cajaAbierta', 'usuario'));
    }

    public function ajaxListadoClientesBusqueda(Request $request)
    {
        if ($request->ajax()) {

            $query = Cliente::select('*');

            if (! is_null($request->input('nit_escogido'))) {
                $nit = $request->input('nit_escogido');
                $query->where('nit', $nit)
                    ->orWhere('cedula', $nit);
            }

            if (! is_null($request->input('nombre_escogido'))) {
                $nombre = $request->input('nombre_escogido');
                $query->where('nombres', 'LIKE', "%$nombre%");
            }

            if (! is_null($request->input('ap_paterno_escogido'))) {
                $paterno = $request->input('ap_paterno_escogido');
                $query->where('ap_paterno', 'LIKE', "%$paterno%");
            }

            if (! is_null($request->input('ap_materno_escogido'))) {
                $materno = $request->input('ap_materno_escogido');
                $query->where('ap_materno', 'LIKE', "%$materno%");
            }

            if (
                ! is_null($request->input('cedula_escogido')) &&
                ! is_null($request->input('nombre_escogido')) &&
                ! is_null($request->input('ap_paterno_escogido')) &&
                ! is_null($request->input('ap_materno_escogido'))
            ) {

                $clientes = $query->limit(5)->get();
            } else {
                $clientes = $query->orderBy('id', 'desc')->limit(10)->get();
            }

            $valores = [
                'listado' => view('factura.ajaxListadoClientesBusqueda')->with(compact('clientes'))->render(),
                'cantidad' => count($clientes),
            ];
            $data = Respuesta::success($valores, 'Datos obtenidos correctamente');
        } else {
            $data = Respuesta::error(null, 'No existe');
        }

        return $data;
    }

    public function emitirRecibo(Request $request)
    {

        if ($request->ajax()) {
            try {

                // dd($request->all());

                $usuario = Auth::user();
                $carroVentas = $request->input('carrito');
                $cliente_id = $request->input('cliente_id');
                $descuento_adicional = $request->input('descuento_adicional');
                $monto_total = (float) $request->input('monto_total');
                $tipo_pago_pagado = $request->input('tipo_pago_pagado');
                $monto_total_pagado = (float) $request->input('monto_total_pagado');
                $monto_pagado = (float) $request->input('monto_pagado');
                $descripcion = $request->input('descripcion');
                $tipo_pago_pagado_2 = $request->input('tipo_pago_pagado_2');
                $monto_pagado_2 = $request->input('monto_pagado_2');

                $cliente = Cliente::with('sucursal')->find($cliente_id);
                $sucursal_objeto = $usuario->sucursal;
                $sucursal_id = $sucursal_objeto->id;

                // if($descuento_adicional > 0){
                //     $totalSubTotales = 0;
                //     foreach ($carroVentas as $item)
                //         $totalSubTotales += (float) $item['subTotal'];

                //     $descuento_adicional = $totalSubTotales - $monto_total;

                // }

                // PREGUNTAMOS POR LA CAJA
                $caja = new Caja;
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

                // AQUI ES PARA QUE PAGUE TOTAL O SOLO UN PAGO
                if (($monto_pagado + $monto_pagado_2) > $monto_total) {
                    $data['estado'] = false;
                    $data['text'] = 'EL MONTO PROPORCIONADO EXEDE AL MONTO DE LA VENTA';

                    return $data;
                }

                $idDetalles = [];
                $esTransporte = false;

                // ================================== COMENZAMOS LA TRANSACCION ==================================
                DB::beginTransaction();

                // ----------------- AGREGAMOS EN L ATABLA DETALLES -----------------
                foreach ($carroVentas as $key => $item) {

                    $servicio = Producto::find($item['servicio_id']);

                    $detalle = new Detalle;
                    $detalle->usuario_creador_id = $usuario->id;
                    $detalle->sucursal_id = $sucursal_id;
                    $detalle->producto_id = $item['servicio_id'];
                    $detalle->usuario_duenio_id = $servicio->usuario_duenio_id;
                    $detalle->nombre_producto = $servicio->nombre;
                    $detalle->descripcion_adicional = $item['descripcion_adicional'];
                    $detalle->precio_compra = $servicio->precio_compra;
                    $detalle->precio = $item['precio'];
                    $detalle->cantidad = $item['cantidad'];
                    $detalle->descuento = $item['descuento'];
                    $detalle->total = $item['total'];
                    $detalle->importe = $item['subTotal'];
                    $detalle->fecha = date('Y-m-d H:i:s');
                    $detalle->estado = 'Pagado';
                    $detalle->save();

                    if ($servicio->control_stock == 1) {
                        // VERIFICAMOS QUE EXISTA EN ALMACEN ANTES DE CONTINUAR
                        $cantidad_almacen = $this->cantidadStockEmpresa($sucursal_id, $item['servicio_id']);

                        if ($cantidad_almacen->estado) {
                            if ($item['cantidad'] > $cantidad_almacen->data['cantidad']) {
                                DB::rollBack();
                                $data = Respuesta::error(null, 'Cantidad Solicitada '.$item['cantidad'].', cantidad en almacen '.$cantidad_almacen->data['cantidad'].' del producto '.$servicio->descripcion);

                                return $data;
                            }
                        } else {
                            $data = $cantidad_almacen;
                            DB::rollBack();

                            return $data;
                        }

                        // AQUI LO MOVEREMOS LOS DETALLES PARA NO HACER OTRO FOR ABAJO
                        $movimiento = new Movimiento;
                        $movimiento->usuario_creador_id = $usuario->id;
                        $movimiento->sucursal_id = $sucursal_objeto->id;
                        $movimiento->producto_id = $servicio->id;
                        $movimiento->detalle_id = $detalle->id;
                        $movimiento->salida = $detalle->cantidad;
                        $movimiento->ingreso = 0;
                        $movimiento->fecha = date('Y-m-d H:i:s');
                        $movimiento->descripcion = 'VENTA';
                        $movimiento->save();
                    }

                    array_push($idDetalles, $detalle->id);
                }

                // ================================== Si todo ha pasado correctamente, hacer commit ==================================
                DB::commit();

                $numeroFacturaRecibo = $this->numeroRecibo($sucursal_objeto->id);
                $numeroFacturaRecibo = ($numeroFacturaRecibo == null ? 1 : ($numeroFacturaRecibo + 1));

                // ESTO ES PARA LA FACTURA LA CREACION
                $facturaVerdad = new Factura;
                $facturaVerdad->usuario_creador_id = Auth::user()->id;
                $facturaVerdad->cliente_id = $cliente->id;
                $facturaVerdad->sucursal_id = $sucursal_objeto->id;
                $facturaVerdad->fecha = date('Y-m-d H:i:s');
                $facturaVerdad->numero_recibo = $numeroFacturaRecibo;
                $facturaVerdad->facturado = 'No';
                $facturaVerdad->total = $monto_total;
                $facturaVerdad->monto_total_subjeto_iva = $monto_total;
                $facturaVerdad->descuento_adicional = $descuento_adicional;
                $facturaVerdad->estado_pago = ($monto_pagado >= $monto_total) ? 'PAGADO' : 'DEUDA';
                $facturaVerdad->descripcion = $descripcion;
                $facturaVerdad->save();

                // AHORA AREMOS PARA LOS DETALLES
                Detalle::whereIn('id', $idDetalles)
                    ->update([
                        'estado' => 'Finalizado',
                        'factura_id' => $facturaVerdad->id,
                    ]);

                // PARA LA TABLA PAGOS
                if ($monto_pagado > 0) {
                    $pago = new Pago;
                    $pago->usuario_creador_id = $usuario->id;
                    $pago->factura_id = $facturaVerdad->id;
                    $pago->sucursal_id = $sucursal_id;
                    $pago->caja_id = $cajaAbierta->id;
                    // $pago->monto              = ($monto_pagado >= $monto_total) ? $monto_total : $monto_pagado;
                    $pago->monto = $monto_pagado;
                    $pago->fecha = $facturaVerdad->fecha;
                    $pago->descripcion = 'VENTA';
                    $pago->apertura_caja = 'No';
                    $pago->tipo_pago = $tipo_pago_pagado;
                    $pago->estado = 'INGRESO';
                    $pago->save();
                }

                // PARA EL PAGO 2
                if (! is_null($tipo_pago_pagado_2) && $monto_pagado_2 > 0) {
                    $pago = new Pago;
                    $pago->usuario_creador_id = $usuario->id;
                    $pago->factura_id = $facturaVerdad->id;
                    $pago->sucursal_id = $sucursal_id;
                    $pago->caja_id = $cajaAbierta->id;
                    $pago->monto = $monto_pagado_2;
                    $pago->fecha = $facturaVerdad->fecha;
                    $pago->descripcion = 'VENTA';
                    $pago->apertura_caja = 'No';
                    $pago->tipo_pago = $tipo_pago_pagado_2;
                    $pago->estado = 'INGRESO';
                    $pago->save();
                }

                $data = Respuesta::success([
                    'url_pdf' => url('factura/imprimeRecibo/'.$facturaVerdad->id),
                    'numero' => $facturaVerdad->id,
                ], 'Se registro con exito el recibo!');
            } catch (\Exception $e) {
                $data = Respuesta::error(null, $e->getMessage().' en '.$e->getFile().':'.$e->getLine());
            }
        } else {
            $data = Respuesta::error(null, 'No existe');
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
            throw new NotFoundHttpException;
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
            throw new NotFoundHttpException;
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

            $usuario = Auth::user();
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

            if ($usuario->rol_id == 2) {
                $query->where('facturas.usuario_creador_id', $usuario->id);
            } else {
                if (! is_null($request->input('buscar_cliente_id'))) {
                    $cliente_id = $request->input('buscar_cliente_id');
                    $query->where('clientes.id', $cliente_id);
                }

                if (! is_null($request->input('buscar_vendedor_id'))) {
                    $vendedor_id = $request->input('buscar_vendedor_id');
                    $query->where('facturas.usuario_creador_id', $vendedor_id);
                }
            }

            if (! is_null($request->input('buscar_sucursal_id'))) {
                $sucursal_id = $request->input('buscar_sucursal_id');
                $query->where('facturas.sucursal_id', $sucursal_id);
            }

            if (! is_null($request->input('buscar_fecha_inicio')) && ! is_null($request->input('buscar_fecha_fin'))) {
                $fecha_ini = $request->input('buscar_fecha_inicio');
                $fecha_fin = $request->input('buscar_fecha_fin');
                $query->whereBetween('facturas.fecha', [$fecha_ini.' 00:00:00', $fecha_fin.' 23:59:59']);
            }

            if (
                ! is_null($request->input('buscar_cliente_id')) &&
                ! is_null($request->input('buscar_vendedor_id')) &&
                ! is_null($request->input('buscar_fecha_inicio')) &&
                ! is_null($request->input('buscar_fecha_fin'))
            ) {
                $facturas = $query->limit(500)->get();
            } else {
                $facturas = $query->orderBy('facturas.id', 'desc')->limit(100)->get();
                // $facturas = $query->orderBy('facturas.id', 'desc')->with('empresa')->get();
            }

            $url_verifica_factura = null;
            $nitEmpresa = null;

            $valores = [
                'listado' => view('factura.ajaxListadoFacturas')->with(compact('facturas', 'url_verifica_factura', 'nitEmpresa'))->render(),
            ];
            $data = Respuesta::success($valores, 'Datos obtenidos correctamente');
        } else {
            // $data['text']   = 'No existe';
            // $data['estado'] = 'error';
            $data = Respuesta::error(null, 'No existe');
        }

        return $data;
    }

    public function ajaxFormularioEditarVenta(Request $request): Respuesta
    {
        if (! $request->ajax()) {
            return Respuesta::error(null, 'No existe');
        }
        $request->validate(['factura_id' => ['required', 'integer']]);
        $factura = Factura::with('cliente', 'detalles.duenio', 'pagos')->findOrFail($request->factura_id);
        $this->verificarEdicionVenta($factura);
        $productos = Producto::with('duenio')->where('tipo', 'PRODUCTO')->where('control_stock', true)->orderBy('nombre')->get();
        $pagado = $factura->pagos->where('estado', 'INGRESO')->sum('monto');
        $version = $this->versionEdicionVenta($factura);

        return Respuesta::success([
            'formulario' => view('factura.ajaxFormularioEditarVenta', compact('factura', 'productos', 'pagado', 'version'))->render(),
        ], 'Datos obtenidos correctamente');
    }

    private function verificarEdicionVenta(Factura $factura): void
    {
        $usuario = Auth::user();
        abort_unless((int) $factura->sucursal_id === (int) $usuario->sucursal_id, 403, 'Edite la venta desde su sucursal.');
        abort_if((int) $usuario->rol_id === 2 && (int) $factura->usuario_creador_id !== (int) $usuario->id, 403);
        if ($factura->estado !== null || $factura->facturado !== 'No') {
            throw ValidationException::withMessages(['venta' => 'Solo se pueden editar ventas vigentes sin factura fiscal.']);
        }
    }

    private function versionEdicionVenta(Factura $factura): string
    {
        return hash('sha256', json_encode([
            $factura->only(['id', 'total', 'descuento_adicional', 'estado', 'facturado']),
            $factura->detalles->sortBy('id')->map(fn ($detalle) => $detalle->only(['id', 'producto_id', 'cantidad', 'precio', 'descuento', 'usuario_duenio_id']))->values()->all(),
            $factura->pagos->sortBy('id')->map(fn ($pago) => $pago->only(['id', 'monto', 'estado', 'tipo_pago']))->values()->all(),
        ], JSON_THROW_ON_ERROR));
    }

    public function guardarEdicionVenta(Request $request): Respuesta
    {
        if (! $request->ajax()) {
            return Respuesta::error(null, 'No existe');
        }
        $datos = $request->validate([
            'factura_id' => ['required', 'integer'],
            'version' => ['required', 'string', 'size:64'],
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.detalle_id' => ['nullable', 'integer'],
            'items.*.producto_id' => ['required', 'integer'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99', 'decimal:0,2'],
        ]);
        $usuario = Auth::user();

        return DB::transaction(function () use ($datos, $usuario) {
            $caja = Caja::where('sucursal_id', $usuario->sucursal_id)->where('estado', 'Abierta')->lockForUpdate()->first();
            if (! $caja) {
                throw ValidationException::withMessages(['venta' => 'Debe abrir una caja en su sucursal para editar la venta.']);
            }
            $factura = Factura::whereKey($datos['factura_id'])->lockForUpdate()->firstOrFail();
            $this->verificarEdicionVenta($factura);
            $factura->load('detalles', 'pagos');
            if (! hash_equals($this->versionEdicionVenta($factura), $datos['version'])) {
                throw ValidationException::withMessages(['venta' => 'La venta o sus pagos cambiaron. Cierre y vuelva a abrir el modal.']);
            }
            $originales = $factura->detalles->keyBy('id');
            $ids = $originales->pluck('producto_id')->merge(collect($datos['items'])->pluck('producto_id'))->unique()->sort();
            $productos = Producto::withTrashed()->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $movimientos = Movimiento::whereIn('detalle_id', $originales->keys())->lockForUpdate()->get();
            $lineas = [];
            $usados = [];
            $cantidades = [];
            $subtotal = 0;
            foreach ($datos['items'] as $item) {
                $detalleId = $item['detalle_id'] ?? null;
                $detalle = $detalleId ? $originales->get($detalleId) : null;
                $producto = $productos->get($item['producto_id']);
                if (! $producto || ($detalleId && (! $detalle || isset($usados[$detalleId]) || (int) $detalle->producto_id !== (int) $item['producto_id']))) {
                    throw ValidationException::withMessages(['items' => 'Revise los productos de esta venta.']);
                }
                if ((! $detalle && ($producto->trashed() || $producto->tipo !== 'PRODUCTO' || ! $producto->control_stock)) || ($producto->trashed() && $item['cantidad'] > $detalle->cantidad)) {
                    throw ValidationException::withMessages(['items' => 'El producto '.$producto->nombre.' no está disponible para agregar.']);
                }
                if ($detalleId) {
                    $usados[$detalleId] = true;
                }
                $cantidad = (int) round($item['cantidad'] * 100);
                $precio = (int) round(($detalle ? $detalle->precio : $producto->precio_venta) * 100);
                $importeBruto = round($cantidad * $precio / 100);
                if ($importeBruto > 999999999999 || $importeBruto < 0) {
                    throw ValidationException::withMessages(['items' => 'La cantidad del producto '.$producto->nombre.' supera el importe permitido.']);
                }
                $bruto = (int) $importeBruto;
                $descuento = $detalle ? (int) round($detalle->descuento * 100) : 0;
                if ($bruto < $descuento || $bruto > 999999999999) {
                    throw ValidationException::withMessages(['items' => 'Revise la cantidad y el descuento del producto '.$producto->nombre.'.']);
                }
                $importe = $bruto - $descuento;
                $subtotal += $importe;
                $cantidades[$producto->id] = ($cantidades[$producto->id] ?? 0) + $cantidad;
                $lineas[] = compact('detalle', 'producto', 'cantidad', 'precio', 'bruto', 'descuento', 'importe');
            }
            $total = $subtotal - (int) round($factura->descuento_adicional * 100);
            $pagado = (int) round($factura->pagos->where('estado', 'INGRESO')->sum('monto') * 100);
            if ($total < 0 || $total > 999999999999) {
                throw ValidationException::withMessages(['items' => 'Revise las cantidades y el descuento general de la venta.']);
            }
            if ($total < $pagado) {
                throw ValidationException::withMessages(['items' => 'El nuevo total no puede ser menor a los '.number_format($pagado / 100, 2).' Bs ya cobrados. Primero debe resolver la devolución del excedente.']);
            }
            foreach ($cantidades as $productoId => $cantidad) {
                if (! $productos[$productoId]->control_stock && ! $movimientos->contains('producto_id', $productoId)) {
                    continue;
                }
                $stock = (new Movimiento)->cantidaDisponile($factura->sucursal_id, $productoId);
                $reservado = $movimientos->where('producto_id', $productoId)->sum('salida') - $movimientos->where('producto_id', $productoId)->sum('ingreso');
                if ($cantidad > (int) round(($stock + $reservado) * 100)) {
                    throw ValidationException::withMessages(['items' => 'Stock insuficiente de '.$productos[$productoId]->nombre.'. Disponible para esta venta: '.number_format($stock + $reservado, 2)]);
                }
            }
            $cuentas = new CuentaHermano;
            $inicio = now()->startOfDay()->format('Y-m-d H:i:s');
            $fin = now()->format('Y-m-d H:i:s');
            $antes = $cuentas->obtener($factura->sucursal_id, $inicio, $fin)['resumen'];
            Movimiento::whereIn('id', $movimientos->pluck('id'))->update(['usuario_eliminador_id' => $usuario->id]);
            Movimiento::whereIn('id', $movimientos->pluck('id'))->delete();
            foreach ($originales as $detalle) {
                if (! isset($usados[$detalle->id])) {
                    $detalle->usuario_eliminador_id = $usuario->id;
                    $detalle->save();
                    $detalle->delete();
                }
            }
            foreach ($lineas as $linea) {
                $producto = $linea['producto'];
                $detalle = $linea['detalle'];
                if (! $detalle) {
                    $detalle = new Detalle;
                    $detalle->usuario_creador_id = $usuario->id;
                    $detalle->factura_id = $factura->id;
                    $detalle->sucursal_id = $factura->sucursal_id;
                    $detalle->producto_id = $producto->id;
                    $detalle->usuario_duenio_id = $producto->usuario_duenio_id;
                    $detalle->nombre_producto = $producto->nombre;
                    $detalle->precio_compra = $producto->precio_compra;
                    $detalle->fecha = $factura->fecha;
                }
                $detalle->usuario_modificador_id = $usuario->id;
                $detalle->cantidad = $linea['cantidad'] / 100;
                $detalle->precio = $linea['precio'] / 100;
                $detalle->descuento = $linea['descuento'] / 100;
                $detalle->total = $linea['bruto'] / 100;
                $detalle->importe = $linea['importe'] / 100;
                $detalle->estado = 'Finalizado';
                $detalle->save();
                if ($producto->control_stock || $movimientos->contains('producto_id', $producto->id)) {
                    Movimiento::create([
                        'usuario_creador_id' => $usuario->id, 'sucursal_id' => $factura->sucursal_id,
                        'producto_id' => $producto->id, 'detalle_id' => $detalle->id,
                        'salida' => $detalle->cantidad, 'ingreso' => 0, 'fecha' => $factura->fecha, 'descripcion' => 'VENTA',
                    ]);
                }
            }
            $factura->total = $total / 100;
            $factura->monto_total_subjeto_iva = $total / 100;
            $factura->estado_pago = $pagado >= $total ? 'PAGADO' : 'DEUDA';
            $factura->usuario_modificador_id = $usuario->id;
            $factura->save();
            $despues = $cuentas->obtener($factura->sucursal_id, $inicio, $fin)['resumen'];
            foreach ($despues as $id => $fila) {
                if ($id === 0) {
                    continue;
                }
                foreach ($fila['medios'] as $medio => $saldo) {
                    if ($saldo < min(0, $antes->get($id)['medios'][$medio] ?? 0)) {
                        throw ValidationException::withMessages(['items' => 'El cambio dejaría sin respaldo una salida de '.$fila['hermano'].' en '.$medio.'. Revise sus movimientos antes de editar.']);
                    }
                }
            }

            return Respuesta::success(['total' => $total / 100, 'saldo' => ($total - $pagado) / 100], 'Venta actualizada correctamente.');
        });
    }

    public function anularRecibo(Request $request)
    {
        if ($request->ajax()) {

            $usuario = Auth::user();
            $factura_id = $request->input('venta');

            $factura = Factura::find($factura_id);
            $factura->usuario_eliminador_id = $usuario->id;
            $factura->estado = 'Anulado';
            $factura->save();

            $detalles = Detalle::where('factura_id', $factura_id)->get();

            $idDetalles = $detalles->pluck('id');

            Movimiento::whereIn('detalle_id', $idDetalles)
                ->update([
                    'usuario_eliminador_id' => $usuario->id,
                ]);

            Movimiento::whereIn('detalle_id', $idDetalles)->delete();

            // ELIMINAMOS LOS DETALLES DE LA FACTURA
            Detalle::where('factura_id', $factura->id)
                ->update([
                    'usuario_eliminador_id' => $usuario->id,
                ]);
            Detalle::where('factura_id', $factura->id)->delete();

            // ELIMINAMOS LOS PAGOS DE LA FACTURA
            Pago::where('factura_id', $factura->id)
                ->update([
                    'usuario_eliminador_id' => $usuario->id,
                ]);
            Pago::where('factura_id', $factura->id)->delete();

            $data = Respuesta::success(null, 'Se elimino con exito el registro.');
        } else {
            $data = Respuesta::error(null, 'No existe');
        }

        return $data;
    }

    // ********************* FUNCIONES PRIVADAS **************
    public function cantidadStockEmpresa($sucursal_id, $id_servicio)
    {
        $movimientoModelo = new Movimiento;
        $stock = $movimientoModelo->cantidaDisponile($sucursal_id, $id_servicio);

        if ($stock > 0) {
            $valores = [
                'cantidad' => $stock,
            ];
            $data = Respuesta::success($valores, 'Cantidad Existente en almacen');
        } else {
            $data = Respuesta::error(null, 'Cantidad no disponible en almacen');
        }

        return $data;
    }
}
