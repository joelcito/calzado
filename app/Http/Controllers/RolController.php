<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    public function listado(){
        return view('rol.listado');
    }

    public function ajaxListado(Request $request){
        if($request->ajax()){
            $roles = Rol::all();
            $valores = [
                'listado' => view('rol.ajaxListado')->with(compact('roles'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarRol(Request $request){
        if($request->ajax()){

            $request->validate([
                'nombre' => 'required',
            ]);

            $id = $request->input('id');

            $nombre  = $request->input('nombre');
            $descripcion  = $request->input('descripcion');
            $usuario = Auth::user();

            if( $id == 0 ){
                $rol                     = new Rol();
                $rol->usuario_creador_id = $usuario->id;
            }else{
                $rol = Rol::find($id);
                $rol->usuario_modificador_id = $usuario->id;
            }

            $rol->nombre = $nombre;
            $rol->descripcion = $descripcion;
            $rol->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminarRol(Request $request){
        if($request->ajax()){

            $id = $request->input('id');
            $usuario = Auth::user();

            $rol = Rol::find($id);
            $rol->usuario_eliminador_id = $usuario->id;
            $rol->save();

            Rol::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
