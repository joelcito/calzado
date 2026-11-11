<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function listado()
    {
        $sucursales = Sucursal::all();
        $categorias = CategoriaProducto::all();
        $usuarios = User::all();

        return view('producto.listado')->with(compact(['sucursales', 'categorias', 'usuarios']));
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {

            $sucursal_id = Auth::user()->sucursal_id; //nuevo
            $sucursal = Sucursal::find($sucursal_id); //nuevo
            $productos = Producto::where('control_stock', true)->orderBy('id', 'desc')->get();
            $valores = [
                'listado' => view('producto.ajaxListado')->with(compact('productos', 'sucursal'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarProducto(Request $request)
    {
        if ($request->ajax()) {

            // dd($request->all());

            $request->validate([
                'nombre' => 'required',
                //'codigo' => 'required|unique:productos,codigo',
                'precio_compra' => 'required',
                'precio_venta'  => 'required',
                // 'minimo_stock'  => 'required',
                'usuario_duenio_id'  => 'required',
                // 'tipo' => 'required',
                'categoria_producto_id' => 'nullable|integer|exists:categoria_productos,id',
            ]);


            // if($tipo == 'PRODUCTO'){
            //     $request->validate([
            //         'minimo_stock'  => 'required',
            //     ]);
            // }

            $id                    = $request->input('id');
            $nombre                = $request->input('nombre');
            $codigo                = $request->input('codigo');
            $precio_compra         = $request->input('precio_compra');
            $precio_venta          = $request->input('precio_venta');
            $minimo_stock          = $request->input('minimo_stock');
            $categoria_producto_id = $request->filled('categoria_producto_id') ? $request->input('categoria_producto_id') : null;
            $usuario_duenio_id     = $request->input('usuario_duenio_id');
            $usuario               = Auth::user();
            $tipo                  = 'PRODUCTO';

            if ($id == 0) {
                /* $request->validate([
                    'codigo' => 'required|unique:productos,codigo',
                ]); */
                $producto                     = new Producto();
                $producto->usuario_creador_id = $usuario->id;
            } else {
                /* if($existe = Producto::where('codigo', $codigo)->where('id', '!=', $id)->first()){
                    $request->validate([
                        'codigo' => 'required|unique:productos,codigo',
                    ]);
                } */
                $producto = Producto::find($id);
                $producto->usuario_modificador_id = $usuario->id;
            }

            $producto->nombre                = $nombre;
            $producto->codigo                = $codigo;
            $producto->precio_compra         = $precio_compra;
            $producto->precio_venta          = $precio_venta;
            $producto->minimo_stock          = $minimo_stock;
            $producto->tipo                  = $tipo;
            $producto->control_stock         = true;
            $producto->categoria_producto_id = $categoria_producto_id;
            $producto->usuario_duenio_id     = $usuario_duenio_id;

            // ==================== LÓGICA PARA LA IMAGEN ====================
            if ($request->hasFile('imagen_producto')) {

                // Si el producto ya tenía una imagen anterior al editar, la eliminamos físicamente
                if ($id != 0 && $producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                    Storage::disk('public')->delete($producto->imagen);
                }

                // Guardamos la nueva imagen en la carpeta 'storage/app/public/productos'
                $rutaImagen = $request->file('imagen_producto')->store('productos', 'public');

                // Guardamos la ruta relativa en la base de datos
                $producto->imagen = $rutaImagen;
            }
            // ===============================================================

            $producto->save();

            // AGREGAMOS EL STOCK DE INGRESO
            if($id == 0){
                $nuevo                     = new Movimiento();
                $nuevo->usuario_creador_id = $usuario->id;
                $nuevo->producto_id        = $producto->id;
                $nuevo->sucursal_id        = $usuario->sucursal_id;
                $nuevo->ingreso            = 1000000;
                $nuevo->salida             = 0;
                $nuevo->fecha              = date('Y-m-d H:i:s');
                $nuevo->descripcion        = "INGRESO AUTOMATICO";
                $nuevo->precio_compra      = $precio_compra;
                $nuevo->precio_venta       = $precio_venta;
                $nuevo->save();
            }

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function eliminarProducto(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->input('id');
            $usuario = Auth::user();

            $producto = Producto::find($id);
            $producto->usuario_eliminador_id = $usuario->id;
            $producto->save();

            Producto::destroy($id);

            $data = Respuesta::success(null, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    //ADICIONES EN MOVIMIENTOS
    public function ajaxStockSucursal(Request $request)
    {
        if ($request->ajax()) {
            $producto_id = $request->input('producto_id');

            $sucursales = Sucursal::withSum(['movimientos' => function ($query) use ($producto_id) {
                $query->where('producto_id', $producto_id);
            }], 'ingreso')
                ->withSum(['movimientos' => function ($query) use ($producto_id) {
                    $query->where('producto_id', $producto_id);
                }], 'salida')
                ->get();
            $producto = Producto::find($producto_id);
            $valores = [
                'listado' => view('producto.ajaxStockSucursal')->with(compact('sucursales', 'producto'))->render()
            ];
            $data = Respuesta::success($valores, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarStockSucursal(Request $request)
    {
        if ($request->ajax()) {

            $request->validate([
                'producto_id' => 'required',
                'sucursal_id' => 'required',
                'f_precio_compra' => 'required',
                'f_precio_venta' => 'required',
                'cantidad_ingreso' => 'required|min:1',
            ]);

            $producto_id = $request->input('producto_id');
            $sucursal_id = $request->input('sucursal_id');
            $precio_compra = $request->input('f_precio_compra');
            $precio_venta = $request->input('f_precio_venta');
            $descripcion     = $request->input('descripcion');
            $usuario         = Auth::user();

            $cantidad_ingreso = $request->input('cantidad_ingreso');

            $producto = Producto::find($producto_id);
            $producto->precio_compra = $precio_compra;
            $producto->precio_venta = $precio_venta;
            $producto->save();

            $nuevo              = new Movimiento();
            $nuevo->producto_id = $producto_id;
            $nuevo->sucursal_id = $sucursal_id;
            $nuevo->ingreso     = $cantidad_ingreso;
            $nuevo->salida             = 0;
            $nuevo->usuario_creador_id = $usuario->id;
            $nuevo->fecha              = date('Y-m-d H:i:s');
            $nuevo->descripcion        = $descripcion;
            $nuevo->precio_compra      = $precio_compra;
            $nuevo->precio_venta       = $precio_venta;
            $nuevo->save();

            $data = Respuesta::success($producto, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function guardarSalidaSucursal(Request $request)
    {
        if ($request->ajax()) {

            $request->validate([
                'salida_producto_id' => 'required',
                'salida_sucursal_id' => 'required',
                'cantidad_salida' => 'required|min:1',
            ]);

            $producto_id = $request->input('salida_producto_id');
            $sucursal_id = $request->input('salida_sucursal_id');
            $descripcion     = $request->input('salida_descripcion');
            $usuario         = Auth::user();

            $cantidad_salida = $request->input('cantidad_salida');

            $nuevo              = new Movimiento();
            $nuevo->producto_id = $producto_id;
            $nuevo->sucursal_id = $sucursal_id;

            $producto = Producto::find($producto_id);

            $nuevo->salida       = $cantidad_salida;
            $nuevo->ingreso             = 0;
            $nuevo->usuario_creador_id = $usuario->id;
            $nuevo->fecha              = date('Y-m-d H:i:s');
            $nuevo->descripcion        = $descripcion;
            $nuevo->save();

            $data = Respuesta::success($producto, "Datos obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "No existe");
        }
        return $data;
    }

    public function ajaxProductosStockMasivo(Request $request)
    {
        if (!$request->ajax()) {

            return response()->json([
                'estado' => false,
                'message' => 'Solicitud no válida.'
            ], 400);
        }


        $request->validate([
            'sucursal_id' => 'required|integer'
        ]);


        $sucursalId = $request->sucursal_id;


        $productos = Producto::query()
            ->select(
                'productos.id',
                'productos.codigo',
                'productos.nombre',
                'productos.precio_compra',
                'productos.precio_venta'
            )
            ->where('tipo', 'PRODUCTO')
            ->whereNull('productos.deleted_at')
            ->orderBy('productos.nombre')
            ->get();


        foreach ($productos as $producto) {

            $ingreso = Movimiento::where('producto_id', $producto->id)
                ->where('sucursal_id', $sucursalId)
                ->sum('ingreso');

            $salida = Movimiento::where('producto_id', $producto->id)
                ->where('sucursal_id', $sucursalId)
                ->sum('salida');

            $producto->stock_actual = (float) $ingreso - (float) $salida;
        }


        return response()->json([
            'estado' => true,
            'productos' => $productos
        ]);
    }

    public function guardarStockMasivo(Request $request)
    {
        if (!$request->ajax()) {

            return response()->json([
                'estado' => false,
                'message' => 'Solicitud no válida.'
            ], 400);
        }


        $request->validate([

            'sucursal_id' => [
                'required',
                'integer'
            ],

            'productos' => [
                'required',
                'array',
                'min:1'
            ],

            'productos.*.producto_id' => [
                'required',
                'integer'
            ],

            'productos.*.cantidad_ingreso' => [
                'required',
                'numeric',
                'gt:0'
            ],

            'productos.*.precio_compra' => [
                'required',
                'numeric',
                'min:0'
            ],

            'productos.*.precio_venta' => [
                'required',
                'numeric',
                'min:0'
            ],

        ]);


        DB::beginTransaction();


        try {

            $usuario = Auth::user();

            $sucursalId =
                $request->sucursal_id;

            $descripcion =
                $request->descripcion;


            $cantidadRegistrados = 0;


            foreach ($request->productos as $item) {


                // ==========================================
                // PRODUCTO
                // ==========================================

                $producto = Producto::findOrFail(
                    $item['producto_id']
                );


                // ACTUALIZAMOS ÚLTIMOS PRECIOS
                $producto->precio_compra =
                    $item['precio_compra'];

                $producto->precio_venta =
                    $item['precio_venta'];

                $producto->save();


                // ==========================================
                // MOVIMIENTO DE INVENTARIO
                // ==========================================

                $movimiento =
                    new Movimiento();

                $movimiento->producto_id =
                    $producto->id;

                $movimiento->sucursal_id =
                    $sucursalId;

                $movimiento->ingreso =
                    $item['cantidad_ingreso'];

                $movimiento->salida = 0;

                $movimiento->usuario_creador_id =
                    $usuario->id;

                $movimiento->fecha =
                    now();

                $movimiento->descripcion =
                    $descripcion
                    ?: 'INGRESO MASIVO DE STOCK';

                $movimiento->precio_compra =
                    $item['precio_compra'];

                $movimiento->precio_venta =
                    $item['precio_venta'];

                $movimiento->save();


                $cantidadRegistrados++;
            }


            DB::commit();


            return response()->json([

                'estado' => true,

                'cantidad' =>
                $cantidadRegistrados,

                'message' =>
                'Stock registrado correctamente.'

            ]);
        } catch (\Throwable $e) {

            DB::rollBack();


            return response()->json([

                'estado' => false,

                'message' =>
                $e->getMessage()

            ], 500);
        }
    }
}
