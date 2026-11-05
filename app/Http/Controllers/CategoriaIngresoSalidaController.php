<?php

namespace App\Http\Controllers;

use App\Models\CategoriaIngresoSalida;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaIngresoSalidaController extends Controller
{
    public function listado()
    {
        return view('categoriaIngresoSalida.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $categorias = CategoriaIngresoSalida::all();
            // $categorias = Categoria::all();
            $valores = [
                'listado' => view('categoriaIngresoSalida.ajaxListado')->with(compact('categorias'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardar(Request $request)
    {
        if ($request->ajax()) {

            // dd($request->all());

            $request->validate([
                'nombre' => 'required',
                'tipo' => 'required',
            ]);

            $id = $request->input('id');

            $nombre      = $request->input('nombre');
            $tipo        = $request->input('tipo');
            $usuario     = Auth::user();

            if ($id == 0) {
                $categoria                     = new CategoriaIngresoSalida();
                $categoria->usuario_creador_id = $usuario->id;
            } else {
                $categoria = CategoriaIngresoSalida::find($id);
                $categoria->usuario_modificador_id = $usuario->id;
            }

            $categoria->nombre      = $nombre;
            $categoria->tipo        = $tipo;
            // $categoria->estado      = "PAGO";
            $categoria->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminar(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->input('id');
            $usuario = Auth::user();

            $categoria = CategoriaIngresoSalida::find($id);
            $categoria->usuario_eliminador_id = $usuario->id;
            $categoria->save();

            CategoriaIngresoSalida::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
