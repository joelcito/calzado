<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SucursalController extends Controller
{
    public function listado(){

        return view('sucursal.listado');
    }

    public function ajaxListado(Request $request){
        if($request->ajax()){
            $sucursales = Sucursal::all();
            $valores = [
                'listado' => view('sucursal.ajaxListado')->with(compact('sucursales'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarSucursal(Request $request){
        if($request->ajax()){

            $request->validate([
                'nombre' => 'required|string',
                'codigo_sucursal' => 'required|integer|min:0',
                'direccion' => 'required|string',
            ]);

            try {
                $nombre          = $request->input('nombre');
                $codigo_sucursal = $request->input('codigo_sucursal');
                $direccion       = $request->input('direccion');
                $sucursal_id     = $request->input('id');
                $usuario         = Auth::user();

                if($sucursal_id == 0){
                    $sucursal                     = new Sucursal();
                    $sucursal->usuario_creador_id = $usuario->id;
                }else{
                    $sucursal                         = Sucursal::find($sucursal_id);
                    $sucursal->usuario_modificador_id = $usuario->id;
                }

                $sucursal->nombre          = $nombre;
                $sucursal->codigo_sucursal = $codigo_sucursal;
                $sucursal->direccion       = $direccion;
                $sucursal->save();

                $data = Respuesta::success(null, "Se proceso con exito");

            } catch (\Exception $e) {
                $data = Respuesta::error(null, "Error un error :". $e->getMessage());
            }

        }else{
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function eliminarSucursal(Request $request){
        if($request->ajax()){

            $id = $request->input('id');
            $usuario = Auth::user();

            $sucursal = Sucursal::find($id);
            $sucursal->usuario_eliminador_id = $usuario->id;
            $sucursal->save();

            Sucursal::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");

        }else{
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
