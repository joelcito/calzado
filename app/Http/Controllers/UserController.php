<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /* NUEVAS FUNCIONALIDADES */
    public function listado()
    {
        $roles = Rol::all();
        $sucursales =  Sucursal::all();
        return view('user.listado')->with(compact(['roles', 'sucursales']));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {

            $usuarios = User::all();
            $valores = [
                'listado' => view('user.ajaxListado')->with(compact('usuarios'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarUsuario(Request $request)
    {
        //TODO: adicionar localidad_id del usuario
        if ($request->ajax()) {

            $request->validate([
                'nombres'        => 'required',
                'ap_paterno'     => 'required',
                'ap_materno'     => 'required',
                'cedula'         => 'required',
                'direccion'      => 'required',
                'email'          => 'required',
                'celular'        => 'required',
                'rol_id'         => 'required',
                'sucursal_id'    => 'required',
            ]);

            $id             = $request->input('id');
            $nombres        = $request->input('nombres');
            $ap_paterno     = $request->input('ap_paterno');
            $ap_materno     = $request->input('ap_materno');
            $cedula         = $request->input('cedula');
            $direccion      = $request->input('direccion');
            $email          = $request->input('email');
            $celular        = $request->input('celular');
            $rol_id         = $request->input('rol_id');
            $sucursal_id    = $request->input('sucursal_id');
            $usuarioLoguado = Auth::user();

            if ($id == 0) {
                $request->validate([
                    'cedula' => 'unique:users,cedula'
                ]);
                $usuario                     = new User();
                $usuario->usuario_creador_id = $usuarioLoguado->id;
                $usuario->password           = Hash::make($cedula);
            } else {
                if ($existe = User::where('cedula', $cedula)->where('id', '!=', $id)->first()) {
                    $request->validate([
                        'cedula' => 'unique:users,cedula'
                    ]);
                }
                $usuario = User::find($id);
                $usuario->usuario_modificador_id = $usuarioLoguado->id;
            }

            $usuario->nombres        = $nombres;
            $usuario->ap_paterno     = $ap_paterno;
            $usuario->ap_materno     = $ap_materno;
            $usuario->cedula         = $cedula;
            $usuario->direccion      = $direccion;
            $usuario->email          = $email;
            $usuario->celular        = $celular;
            $usuario->rol_id         = $rol_id;
            $usuario->sucursal_id    = $sucursal_id;
            $usuario->name           = $nombres . " " . $ap_paterno . " " . $ap_materno;
            $usuario->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminarUsuario(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->input('id');
            $usuarioLogueado = Auth::user();

            $usuario = User::find($id);
            $usuario->usuario_eliminador_id = $usuarioLogueado->id;
            $usuario->save();

            User::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function resetPassword(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'usuario_id' => 'required|exists:users,id',
                'password' => 'required|min:6|confirmed',
            ]);

            $usuario_id = $request->usuario_id;
            $password = $request->password;

            $usuario = User::find($usuario_id);
            $usuario->password = Hash::make($password);
            $usuario->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
