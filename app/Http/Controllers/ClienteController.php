<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function listado()
    {
        return view('cliente.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $clientes = Cliente::with('sucursal')->get();
            $valores = [
                'listado' => view('cliente.ajaxListado')->with(compact('clientes'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarCliente(Request $request)
    {
        if ($request->ajax()) {

            $request->validate([
                'nombres'        => 'required',
                // 'sucursal_id'        => 'required',
                // 'direccion'        => 'required',
                'ap_paterno'     => 'required',
                //'ap_materno'     => 'required',
                // 'cedula'         => 'required',
                /* 'complemento'    => 'required',
                'nit'            => 'required',
                'razon_social'   => 'required',
                'correo'         => 'required',
                'numero_celular' => 'required', */
            ]);

            $id             = $request->input('id');
            $nombres        = $request->input('nombres');
            $ap_paterno     = $request->input('ap_paterno');
            $ap_materno     = $request->input('ap_materno');
            $cedula         = $request->input('cedula');
            $complemento    = $request->input('complemento');
            $nit            = $request->input('nit');
            $razon_social   = $request->input('razon_social');
            $correo         = $request->input('correo');
            $numero_celular = $request->input('numero_celular');
            $direccion = $request->input('direccion');
            $usuario        = Auth::user();

            if ($id == 0) {
                // $request->validate([
                //     'cedula' => 'unique:clientes,cedula'
                // ]);
                $cliente                     = new Cliente();
                $cliente->usuario_creador_id = $usuario->id;
            } else {
                // if( $existe = Cliente::where('cedula', $cedula)->where('id', '!=', $id)->first() ){
                //     $request->validate([
                //         'cedula' => 'unique:users,cedula'
                //     ]);
                // }
                $cliente = Cliente::find($id);
                $cliente->usuario_modificador_id = $usuario->id;
            }

            $cliente->nombres = $nombres;
            $cliente->ap_paterno = $ap_paterno;
            $cliente->ap_materno = $ap_materno;
            $cliente->cedula = $cedula;
            $cliente->complemento = $complemento;
            $cliente->nit = $nit;
            $cliente->razon_social = $razon_social;
            $cliente->correo = $correo;
            $cliente->numero_celular = $numero_celular;
            $cliente->direccion = $direccion;
            $cliente->save();

            $valores = [
                'cliente' => $cliente
            ];

            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminarCliente(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->input('id');
            $usuario = Auth::user();

            $cliente = Cliente::find($id);
            $cliente->usuario_eliminador_id = $usuario->id;
            $cliente->save();

            Cliente::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
