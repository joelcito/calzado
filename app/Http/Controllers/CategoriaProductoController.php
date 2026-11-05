<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaProductoController extends Controller
{
    public function listado()
    {

        return view('categoria.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $categorias = CategoriaProducto::all();
            $valores = [
                'listado' => view('categoria.ajaxListado')->with(compact('categorias'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarCategoria(Request $request)
    {
        if ($request->ajax()) {

            $request->validate([
                'nombre' => 'required',
            ]);

            $id = $request->input('id');
            $nombre      = $request->input('nombre');
            $usuario     = Auth::user();

            if ($id == 0) {
                $categoria                     = new CategoriaProducto();
                $categoria->usuario_creador_id = $usuario->id;
            } else {
                $categoria = CategoriaProducto::find($id);
                $categoria->usuario_modificador_id = $usuario->id;
            }

            $categoria->nombre = $nombre;
            $categoria->save();

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminarCategoria(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->input('id');
            $usuario = Auth::user();

            $categoria = CategoriaProducto::find($id);
            $categoria->usuario_eliminador_id = $usuario->id;
            $categoria->save();

            CategoriaProducto::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }
}
