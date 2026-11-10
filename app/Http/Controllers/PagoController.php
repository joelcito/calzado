<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CategoriaIngresoSalida;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use App\Utils\CuentaHermano;
use App\Utils\Respuesta;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class PagoController extends Controller
{
    public function listadoHermanos(): \Illuminate\Contracts\View\View
    {
        $usuario = Auth::user();
        $usuarios = User::all();
        $sucursales = Sucursal::all();
        $fechaIni = now()->toDateString();
        $fechaFin = $fechaIni;
        $cajaAbierta = (new Caja)->sacaCajaVigente($usuario->sucursal_id);
        $categoriasIngreso = CategoriaIngresoSalida::where('tipo', 'INGRESO')->get();
        $categoriasSalida = CategoriaIngresoSalida::where('tipo', 'SALIDA')->get();

        return view('pago.listadoHermanos', compact('usuario', 'usuarios', 'sucursales', 'fechaIni', 'fechaFin', 'cajaAbierta', 'categoriasIngreso', 'categoriasSalida'));
    }

    public function ajaxListadoHermanos(Request $request): Respuesta
    {
        if (! $request->ajax()) {
            return Respuesta::error(null, 'Error al obtener los datos');
        }
        $request->validate([
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursales,id'],
            'usuario_duenio_id' => ['nullable', 'integer', 'exists:users,id'],
            'fecha_ini' => ['required', 'date_format:Y-m-d'],
            'fecha_fin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecha_ini', 'before_or_equal:today'],
        ]);
        $sucursalId = (int) ($request->input('sucursal_id') ?: Auth::user()->sucursal_id);
        $datos = (new CuentaHermano)->obtener($sucursalId, $request->fecha_ini.' 00:00:00', $request->fecha_fin.' 23:59:59');
        if ($request->filled('usuario_duenio_id')) {
            $id = (int) $request->usuario_duenio_id;
            $datos['resumen'] = $datos['resumen']->filter(fn ($fila) => $fila['hermano_id'] === $id);
            $datos['movimientos'] = $datos['movimientos']->filter(fn ($fila) => $fila['hermano_id'] === $id);
        }

        return Respuesta::success([
            'listado' => view('pago.ajaxListadoHermanos', $datos)->render(),
        ], 'Datos obtenidos correctamente');
    }

    /* Cuentas por cobrar */
    public function listadoDeuda()
    {

        $usuario = Auth::user();
        $sucursal = $usuario->sucursal;

        $fechaIni = '';
        $fechaFin = '';

        // if ($usuario->isAdmin()) {
        $usuarios = User::all();
        $sucursales = Sucursal::all();
        // } else {
        //     $usuarios   = User::where('id', $usuario->id)->get();
        //     $sucursales = Sucursal::where('id', $sucursal->id)->get();
        // }

        $caja = new Caja;
        $cajaAbierta = $caja->sacaCajaVigente($sucursal->id);

        return view('pago.listadoDeuda')->with(compact('sucursales', 'fechaIni', 'fechaFin', 'usuarios', 'cajaAbierta', 'usuario'));
    }

    public function ajaxListadoDeuda(Request $request)
    {
        if ($request->ajax()) {

            // dd($request->all());

            $sucursal_id = $request->input('sucursal_id');
            $buscar_nombre_cliente = $request->input('buscar_nombre_cliente');
            $buscar_ap_paterno_cliente = $request->input('buscar_ap_paterno_cliente');
            $buscar_ap_materno_cliente = $request->input('buscar_ap_materno_cliente');
            $buscar_numero_celular = $request->input('buscar_numero_celular');
            $buscar_nro_cedula = $request->input('buscar_nro_cedula');
            $buscar_fecha_inicio = $request->input('buscar_fecha_inicio');
            $buscar_fecha_fin = $request->input('buscar_fecha_fin');
            $vendedor_id = $request->input('vendedor_id');
            $codigo_venta = $request->input('codigo_venta');

            // $query = Factura::select('*')
            //                 ->join('sucursales', 'sucursales.id', '=', 'facturas.sucursal_id')
            //                 ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id');

            // $facturas = Factura::with(['cliente', 'sucursal'])->where('estado_pago', 'DEUDA')->orderBy('id', 'desc')->get();
            $query = Factura::with(['cliente', 'sucursal'])->where('estado_pago', 'DEUDA')->orderBy('id', 'desc');

            if (! is_null($sucursal_id)) {
                $query->where('sucursal_id', $sucursal_id);
            }

            if (! is_null($vendedor_id)) {
                $query->where('usuario_creador_id', $vendedor_id);
            }

            if (! is_null($codigo_venta)) {
                // $query->where('codigo_venta', $codigo_venta);
                $query->where('codigo_venta', 'like', "%$codigo_venta%");
            }

            if (! is_null($buscar_nombre_cliente)) {
                $buscar = $buscar_nombre_cliente;
                $query->whereHas('cliente', function ($q) use ($buscar) {
                    $q->where('nombres', 'like', "%$buscar%");
                });
            }

            if (! is_null($buscar_ap_paterno_cliente)) {
                $buscar = $buscar_ap_paterno_cliente;
                $query->whereHas('cliente', function ($q) use ($buscar) {
                    $q->where('ap_paterno', 'like', "%$buscar%");
                });
            }

            if (! is_null($buscar_ap_materno_cliente)) {
                $buscar = $buscar_ap_materno_cliente;
                $query->whereHas('cliente', function ($q) use ($buscar) {
                    $q->where('ap_materno', 'like', "%$buscar%");
                });
            }

            if (! is_null($buscar_numero_celular)) {
                $buscar = $buscar_numero_celular;
                $query->whereHas('cliente', function ($q) use ($buscar) {
                    $q->where('numero_celular', "%$buscar%");
                });
            }

            if (! is_null($buscar_nro_cedula)) {
                $buscar = $buscar_nro_cedula;
                $query->whereHas('cliente', function ($q) use ($buscar) {
                    $q->where('cedula', 'like', "%$buscar%");
                });
            }

            if (! is_null($buscar_fecha_inicio) && ! is_null($buscar_fecha_fin)) {
                $fecha_ini = $buscar_fecha_inicio;
                $fecha_fin = $buscar_fecha_fin;
                $query->whereBetween('fecha', [$fecha_ini.' 00:00:00', $fecha_fin.' 23:59:59']);
            }

            $query->whereNull('estado');

            if (
                ! is_null($sucursal_id) &&
                ! is_null($buscar_nombre_cliente) &&
                ! is_null($buscar_ap_paterno_cliente) &&
                ! is_null($buscar_ap_materno_cliente) &&
                ! is_null($buscar_numero_celular) &&
                ! is_null($buscar_nro_cedula) &&
                ! is_null($buscar_fecha_inicio) &&
                ! is_null($buscar_fecha_fin)
            ) {
                $facturas = $query->limit(500)->get();
            } else {
                $facturas = $query->orderBy('facturas.id', 'desc')->limit(100)->get();
                // $facturas = $query->orderBy('facturas.id', 'desc')->with('empresa')->get();
            }

            $valores = [
                'listado' => view('pago.ajaxListadoDeuda')->with(compact('facturas'))->render(),
            ];
            $data = Respuesta::success($valores, 'Datos obtenidos correctamente');
        } else {
            $data = Respuesta::error(null, 'Error al obtener los datos');
        }

        return $data;
    }

    public function ajaxFormPagoDeuda(Request $request)
    {
        if ($request->ajax()) {
            $factura_id = $request->input('factura_id');

            $factura = Factura::with(['cliente', 'sucursal'])->where('id', $factura_id)->first();
            $pagos = Pago::where('factura_id', $factura_id)
                ->where('estado', 'INGRESO')
                ->get();
            $pagado = Pago::where('factura_id', $factura_id)
                ->where('estado', 'INGRESO')
                ->sum('monto');

            $valores = [
                'formulario' => view('pago.ajaxFormPagoDeuda')->with(compact('factura', 'pagos', 'pagado'))->render(),
            ];
            $data = Respuesta::success($valores, 'Datos obtenidos correctamente');
        } else {
            $data = Respuesta::error(null, 'Error al obtener los datos');
        }

        return $data;
    }

    public function guardarPagoDeuda(Request $request)
    {
        if ($request->ajax()) {

            $request->validate([
                'factura_id' => 'required',
                'tipo_pago' => 'required',
                'importe_pago' => 'required',
                'saldo' => 'required',
            ]);

            $factura_id = $request->input('factura_id');
            $tipo_pago = $request->input('tipo_pago');
            $importe_pago = $request->input('importe_pago');
            $saldo = $request->input('saldo');
            $usuario = Auth::user();
            $sucursal = $usuario->sucursal;
            $caja = new Caja;
            $cajaVigente = $caja->sacaCajaVigente($sucursal->id);

            if ($importe_pago > 0 && $importe_pago <= $saldo) {
                $nuevo = new Pago;
                $nuevo->usuario_creador_id = $usuario->id;
                $nuevo->factura_id = $factura_id;
                $nuevo->sucursal_id = $sucursal->id;
                $nuevo->caja_id = $cajaVigente->id;
                $nuevo->monto = $importe_pago;
                $nuevo->cambio = 0;
                $nuevo->fecha = date('Y-m-d H:i:s');
                $nuevo->descripcion = 'VENTA';
                $nuevo->apertura_caja = 'No';
                $nuevo->tipo_pago = $tipo_pago;
                $nuevo->estado = 'INGRESO';
                $nuevo->save();

                if (($saldo - $importe_pago) == 0) {
                    $factura = Factura::find($factura_id);
                    $factura->estado_pago = 'PAGADO';
                    $factura->save();

                    // // VERIFICAMOS LA AGENDA ATENDIDA
                    // $agenda = Agenda::where('factura_id', $factura_id)->first();
                    // if ($agenda) {
                    //     $agenda->usuario_modificador_id = $usuario->id;
                    //     $agenda->estado                 = "ATENDIDO";
                    //     $agenda->save();
                    // }
                }

                $data = Respuesta::success(null, 'Datos obtenidos correctamente');
            } else {
                $data = Respuesta::error(null, 'El importe debe ser mayor a 0 y menor al saldo.');
            }
        } else {
            $data = Respuesta::error(null, 'Error en registro de datos.');
        }

        return $data;
    }

    public function listado(Request $request)
    {

        $usuario = Auth::user();
        $sucursal = $usuario->sucursal;

        $fechaIni = date('Y-m-d');
        $fechaFin = date('Y-m-d');

        $usuarios = User::all();
        $sucursales = Sucursal::all();

        $caja = new Caja;
        $cajaAbierta = $caja->sacaCajaVigente($sucursal->id);

        // dd($usuario->isAdministrador());

        // CAJAS APERTURADAS
        $cajas = $caja->cajasUsuario($usuario->id, $usuario->isAdministrador(), $sucursal->id);
        // dd($cajas);

        $categoriasIngreso = CategoriaIngresoSalida::where('tipo', 'INGRESO')->get();

        $categoriasSalida = CategoriaIngresoSalida::where('tipo', 'SALIDA')->get();

        return view('pago.listado')->with(compact('sucursales', 'fechaIni', 'fechaFin', 'usuarios', 'cajaAbierta', 'cajas', 'usuario', 'categoriasIngreso', 'categoriasSalida'));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {

            $sucursal_id = $request->input('sucursal_id');
            $fecha_ini = $request->input('fecha_ini');
            $fecha_fin = $request->input('fecha_fin');
            $usuario_id = $request->input('usuario_busqueda_id');
            $caja_id = $request->input('caja_id');
            $usuario = Auth::user();

            $query = Pago::select();

            if ($sucursal_id != null) {
                $query->where('sucursal_id', $sucursal_id);
            } else {
                // PARA LAS SUCURSALES
                $sucursal = $usuario->sucursal;
                $query->where('sucursal_id', $sucursal->id);
            }

            if ($caja_id != null) {
                $query->where('caja_id', $caja_id);
            }

            if ($fecha_ini != null && $fecha_fin != null) {
                $query->where('fecha', '>=', $fecha_ini.' 00:00:00')
                    ->where('fecha', '<=', $fecha_fin.' 23:59:59');
            }

            if ($usuario_id != null) {
                $query->where('usuario_creador_id', $usuario_id);
            }

            $pagos = $query->orderBy('id', 'desc')->get();
            $valores = [
                'listado' => view('pago.ajaxListado')->with(compact('pagos'))->render(),
            ];
            $data = Respuesta::success($valores, 'Datos obtenidos correctamente');
        } else {
            $data = Respuesta::error(null, 'Error al obtener los datos');
        }

        return $data;
    }

    public function guardarTipoIngresoSalida(Request $request)
    {
        if (! $request->ajax()) {
            return Respuesta::error(null, 'Error al obtener los datos');
        }
        $request->validate([
            'usuario_duenio_id' => ['required', 'integer', 'exists:users,id'],
            'tipo' => ['required', Rule::in(['INGRESO', 'SALIDA'])],
            'tipo_pago' => ['required', Rule::in(CuentaHermano::MEDIOS)],
            'monto' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99', 'decimal:0,2'],
            'categoria_id' => ['required', 'integer', Rule::exists('categoria_ingreso_salidas', 'id')->where('tipo', $request->input('tipo'))->whereNull('deleted_at')],
            'descripcion' => ['required', 'string', 'max:1000'],
        ]);
        $usuario = Auth::user();

        return DB::transaction(function () use ($request, $usuario) {
            $caja = Caja::where('sucursal_id', $usuario->sucursal_id)->where('estado', 'Abierta')->lockForUpdate()->first();
            if (! $caja) {
                return Respuesta::error(null, 'Debe abrir una caja en su sucursal para registrar movimientos.');
            }
            if ($request->tipo === 'SALIDA') {
                $cuenta = (new CuentaHermano)->obtener($usuario->sucursal_id, now()->startOfDay()->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s'));
                $saldo = $cuenta['resumen']->get((int) $request->usuario_duenio_id)['medios'][$request->tipo_pago] ?? 0;
                if ((int) round($request->monto * 100) > $saldo) {
                    return Respuesta::error(null, 'Saldo insuficiente del hermano en '.$request->tipo_pago.'. Disponible: '.number_format($saldo / 100, 2).' Bs.');
                }
            }
            $pago = new Pago;
            $pago->usuario_creador_id = $usuario->id;
            $pago->usuario_duenio_id = $request->usuario_duenio_id;
            $pago->sucursal_id = $usuario->sucursal_id;
            $pago->caja_id = $caja->id;
            $pago->categoria_ingreso_salida_id = $request->categoria_id;
            $pago->monto = $request->monto;
            $pago->fecha = now();
            $pago->descripcion = $request->descripcion;
            $pago->apertura_caja = 'No';
            $pago->tipo_pago = $request->tipo_pago;
            $pago->estado = $request->tipo;
            $pago->cambio = 0;
            $pago->save();

            return Respuesta::success(null, 'Movimiento del hermano registrado correctamente.');
        });
    }

    public function comprobantePago(Request $request, $pago_id)
    {

        $pago = Pago::find($pago_id);

        $html = View::make('pago.pdf.comprobantePago', compact(['pago']))->render();
        $dompdf = new Dompdf;
        $dompdf->setPaper([0, 0, 300.00, 504.00], 'landscape'); // cambio orientacion de la hoja
        $dompdf->loadHtml($html);
        $dompdf->render();
        // return $dompdf->stream('Reporte_Ingresos.pdf');

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename=Cotizacion.pdf');

        // dd($request->all(), $pago_id);

    }
}
