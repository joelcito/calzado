<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Pago;
use App\Models\User;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function listado(Request $request)
    {

        $usuario = Auth::user();
        $sucursal = $usuario->sucursal;

        $caja = new Caja();
        $cajaAbierta = $caja->sacaCajaVigente($sucursal->id);

        $usuarios = User::all();

        return view('caja.listado')->with(compact('cajaAbierta', 'usuario', 'usuarios'));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $usuario = Auth::user();
            $fechaIni = date('Y-m-d') . ' 00:00:00';
            $fechaFin = date('Y-m-d') . ' 23:59:59';
            // if (in_array($usuario->rol_id, [1, 5])) {
            //     $cajas = Caja::where('sucursal_id', $usuario->sucursal_id)->orderBy('id', 'desc')->get();
            // } elseif (in_array($usuario->rol_id, [2, 6])) {
            //     $cajas = Caja::where('usuario_id', $usuario->id)
            //         ->whereBetween('fecha_apertura', [$fechaIni, $fechaFin])
            //         ->orderBy('id', 'desc')->get();
            // } else {
            //     $cajas = Caja::where('usuario_id', $usuario->id)
            //         ->whereBetween('fecha_apertura', [$fechaIni, $fechaFin])
            //         ->orderBy('id', 'desc')->get();
            // }
            if ($usuario->rol_id == 4) { //ENTONCES SIGINIFICA QUE ES CHOFER
                $cajas = Caja::where('usuario_id', $usuario->id)
                    // ->whereBetween('fecha_apertura', [$fechaIni, $fechaFin])
                    ->orderBy('id', 'desc')->get();
            } else {
                // $cajas = Caja::where('sucursal_id', $usuario->sucursal_id)->orderBy('id', 'desc')->get();
                $cajas = Caja::where('sucursal_id', $usuario->sucursal_id)
                    // ->whereBetween('fecha_apertura', [$fechaIni, $fechaFin])
                    ->orderBy('id', 'desc')
                    ->get();
            }
            $valores = [
                'listado' => view('caja.ajaxListado')->with(compact('cajas'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarAperturaCaja(Request $request)
    {
        if ($request->ajax()) {

            $usuario = Auth::user();
            $usuario_id     = $request->input('usuario_id');
            $monto_apertura = $request->input('monto_apertura');
            $descripcion    = $request->input('descripcion');

            $caja                     = new Caja();
            $caja->usuario_creador_id = $usuario->id;
            $caja->usuario_apertura_id         = $usuario_id;
            $caja->sucursal_id        = $usuario->sucursal_id;
            $caja->fecha_apertura     = date('Y-m-d H:i:s');
            $caja->monto_apertura     = $monto_apertura;
            $caja->descripcion        = $descripcion;
            $caja->estado             = 'Abierta';
            $caja->save();

            if ($monto_apertura > 0) {
                $pago                     = new Pago();
                $pago->usuario_creador_id = $usuario->id;
                $pago->caja_id            = $caja->id;
                $pago->sucursal_id        = $usuario->sucursal_id;
                $pago->monto              = $monto_apertura;
                $pago->fecha              = date('Y-m-d H:i:s');
                $pago->descripcion        = $descripcion;
                $pago->apertura_caja      = 'Si';
                $pago->tipo_pago          = 'EFECTIVO';
                $pago->estado             = 'INGRESO';
                $pago->save();
            }

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarCerrarCaja(Request $request)
    {

        if ($request->ajax()) {

            $usuario            = Auth::user();
            $usuario_id_cierre  = $request->input('usuario_id_cierre');
            $caja_id_cierre     = $request->input('caja_id_cierre');
            $monto_cierre       = $request->input('monto_cierre');
            $descripcion_cierre = $request->input('descripcion_cierre');
            $fecha_registro     = date('Y-m-d H:i:s');

            $cajaAperturada = Caja::where('id', $caja_id_cierre)
                ->where('estado', 'Abierta')
                ->first();

            if ($cajaAperturada) {

                // TRONCALES
                $pagosVenta = Pago::where('apertura_caja', 'No')
                    ->where('caja_id', $cajaAperturada->id)
                    ->where('estado', 'INGRESO')
                    ->whereNotNull('factura_id')
                    ->get();

                $pagosOtros = Pago::where('apertura_caja', 'No')
                    ->where('caja_id', $cajaAperturada->id)
                    ->where('estado', 'INGRESO')
                    ->whereNull('factura_id')
                    ->get();

                // TOTAL APERTURA CAJA
                $totalAperturaCaja = Pago::where('apertura_caja', 'Si')
                    ->where('caja_id', $cajaAperturada->id)
                    ->where('estado', 'INGRESO')
                    ->sum('monto');

                // SALIDA
                $totalSalidas = Pago::where('apertura_caja', 'No')
                    ->where('caja_id', $cajaAperturada->id)
                    ->where('estado', 'SALIDA')
                    ->sum('monto');

                // TOTAL RECAUDADO
                $totalRecaudado = Pago::where('caja_id', $cajaAperturada->id)
                    ->where('estado', 'INGRESO')
                    ->sum('monto');

                $totalRecaudado = $totalRecaudado - $totalSalidas;

                // dd(
                //     $pagosVenta,
                //     $pagosOtros,
                //     $totalAperturaCaja,
                //     $totalSalidas,
                //     $totalRecaudado
                // );

                // INGRESOS DE VENTAS
                $totalIngresosVenta              = $pagosVenta->sum('monto');
                $totalIngresosVentaEfectivo      = $pagosVenta->where('tipo_pago', 'EFECTIVO')->sum('monto');
                $totalIngresosVentaTramsferencia = $pagosVenta->where('tipo_pago', 'TRANSFERENCIA')->sum('monto');
                $totalIngresosVentaEQr           = $pagosVenta->where('tipo_pago', 'QR')->sum('monto');

                // OTROS INGRESOS
                $totalOtrosIngresos                   = $pagosOtros->sum('monto');
                $totalOtrosIngresosVentaEfectivo      = $pagosOtros->where('tipo_pago', 'EFECTIVO')->sum('monto');
                $totalOtrosIngresosVentaTramsferencia = $pagosOtros->where('tipo_pago', 'TRANSFERENCIA')->sum('monto');
                $totalOtrosIngresosVentaEQr           = $pagosOtros->where('tipo_pago', 'QR')->sum('monto');

                $cajaAperturada->fecha_cierre           = $fecha_registro;
                $cajaAperturada->usuario_cierre_id      = $usuario->id;
                $cajaAperturada->monto_cierre           = $monto_cierre;
                $cajaAperturada->descripcion_cierre     = $descripcion_cierre;
                $cajaAperturada->total_venta            = $totalIngresosVenta;
                $cajaAperturada->venta_contado          = $totalIngresosVentaEfectivo;
                $cajaAperturada->otro_ingreso           = $totalOtrosIngresos;
                $cajaAperturada->total_ingreso          = $totalRecaudado;
                $cajaAperturada->total_qr               = $totalIngresosVentaEQr;
                $cajaAperturada->total_transferencia    = $totalIngresosVentaTramsferencia;
                $cajaAperturada->total_salida           = $totalSalidas;
                $cajaAperturada->saldo                  = $monto_cierre + $totalIngresosVentaTramsferencia + $totalIngresosVentaEQr - $totalRecaudado;
                $cajaAperturada->usuario_modificador_id = $usuario->id;
                $cajaAperturada->estado                 = 'Cerrado';

                // dd($cajaAperturada);

                $cajaAperturada->save();


                $data = Respuesta::success(null, "Datos obtenidos correctamente");
            } else {
                $data = Respuesta::error(null, "No se encontro la caja o la caja esta en un estado cerrado");
            }
        } else {

            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    // public function habilitarCaja(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $id = $request->input('id');
    //         $usuario = Auth::user();

    //         $caja = Caja::find($id);
    //         $caja->usuario_modificador_id = $usuario->id;
    //         $caja->estado = 'Abierta';
    //         $caja->save();

    //         $data = Respuesta::success(null, "Datos obtenidos correctamente");
    //     } else {
    //         $data = Respuesta::error(null, "No existe");
    //     }
    //     return $data;
    // }

    public function formularioEdicionCaja(Request $request)
    {

        if ($request->ajax()) {

            $caja_editar_id             = $request->input('caja_editar_id');
            $usuario_apertura_id        = $request->input('usuario_apertura_id');
            $descripcion_apertura       = $request->input('descripcion_apertura');
            $usuario_cierre_id_edicion  = $request->input('usuario_cierre_id_edicion');
            $descripcion_cierre_edicion = $request->input('descripcion_cierre_edicion');
            $estado_caja                = $request->input('estado_caja');

            $caja                      = Caja::find($caja_editar_id);
            $caja->usuario_apertura_id = $usuario_apertura_id;
            $caja->descripcion         = $descripcion_apertura;
            $caja->usuario_cierre_id   = $usuario_cierre_id_edicion;
            $caja->descripcion_cierre  = $descripcion_cierre_edicion;
            $caja->estado              = $estado_caja;

            $caja->save();
            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function verCaja(Request $request)
    {

        if ($request->ajax()) {

            $caja_id = $request->input('caja');

            $caja = Caja::find($caja_id);

            // dd($request->all());

            // TRONCALES
            $pagosVenta = Pago::where('apertura_caja', 'No')
                ->where('caja_id', $caja->id)
                ->where('estado', 'INGRESO')
                ->whereNotNull('factura_id')
                ->get();

            $pagosOtros = Pago::where('apertura_caja', 'No')
                ->where('caja_id', $caja->id)
                ->where('estado', 'INGRESO')
                ->whereNull('factura_id')
                ->get();

            // TOTAL APERTURA CAJA
            $totalAperturaCaja = Pago::where('apertura_caja', 'Si')
                ->where('caja_id', $caja->id)
                ->where('estado', 'INGRESO')
                ->sum('monto');

            // SALIDA
            $totalSalidas = Pago::where('apertura_caja', 'No')
                ->where('caja_id', $caja->id)
                ->where('estado', 'SALIDA')
                ->sum('monto');

            // INGRESOS DE VENTAS
            $totalIngresosVenta              = $pagosVenta->sum('monto');
            $totalIngresosVentaEfectivo      = $pagosVenta->where('tipo_pago', 'EFECTIVO')->sum('monto');
            $totalIngresosVentaTramsferencia = $pagosVenta->where('tipo_pago', 'TRANSFERENCIA')->sum('monto');
            $totalIngresosVentaEQr           = $pagosVenta->where('tipo_pago', 'QR')->sum('monto');

            // OTROS INGRESOS
            $totalOtrosIngresos                   = $pagosOtros->sum('monto');
            $totalOtrosIngresosVentaEfectivo      = $pagosOtros->where('tipo_pago', 'EFECTIVO')->sum('monto');
            $totalOtrosIngresosVentaTramsferencia = $pagosOtros->where('tipo_pago', 'TRANSFERENCIA')->sum('monto');
            $totalOtrosIngresosVentaEQr           = $pagosOtros->where('tipo_pago', 'QR')->sum('monto');


            $valores = [
                'listado' => view('caja.verCaja')->with(compact(
                    'totalIngresosVenta',
                    'totalIngresosVentaEfectivo',
                    'totalIngresosVentaTramsferencia',
                    'totalIngresosVentaEQr',

                    'totalOtrosIngresos',
                    'totalOtrosIngresosVentaEfectivo',
                    'totalOtrosIngresosVentaTramsferencia',
                    'totalOtrosIngresosVentaEQr',

                    'totalAperturaCaja',

                    'totalSalidas'

                ))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }
}
