<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaIngresoSalidaController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect('home');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

    Route::prefix('/rol')->group(function () {
        Route::get('/listado', [RolController::class, 'listado'])->name('rol.listado');
        Route::post('/ajaxListado', [RolController::class, 'ajaxListado'])->name('rol.ajaxListado');
        Route::post('/guardarRol', [RolController::class, 'guardarRol'])->name('rol.guardarRol');
        Route::post('/eliminarRol', [RolController::class, 'eliminarRol'])->name('rol.eliminarRol');
    });

    // SUCURSALES
    Route::prefix('/sucursal')->group(function () {
        Route::get('/listado', [SucursalController::class, 'listado'])->name('sucursal.listado');
        Route::post('/ajaxListado', [SucursalController::class, 'ajaxListado'])->name('sucursal.ajaxListado');
        Route::post('/guardarSucursal', [SucursalController::class, 'guardarSucursal'])->name('sucursal.guardarSucursal');
        Route::post('/eliminarSucursal', [SucursalController::class, 'eliminarSucursal'])->name('sucursal.eliminarSucursal');
    });

    Route::prefix('/usuario')->group(function () {
        Route::get('/listado', [UserController::class, 'listado'])->name('usuario.listado');
        Route::post('/ajaxListado', [UserController::class, 'ajaxListado'])->name('usuario.ajaxListado');
        Route::post('/guardarUsuario', [UserController::class, 'guardarUsuario'])->name('usuario.guardarUsuario');
        Route::post('/eliminarUsuario', [UserController::class, 'eliminarUsuario'])->name('usuario.eliminarUsuario');
        Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('usuario.resetPassword');
    });

    // CATEGORIA
    Route::prefix('/categoria')->group(function () {
        Route::get('/listado', [CategoriaProductoController::class, 'listado'])->name('categoria.listado');
        Route::post('/ajaxListado', [CategoriaProductoController::class, 'ajaxListado'])->name('categoria.ajaxListado');
        Route::post('/guardarCategoria', [CategoriaProductoController::class, 'guardarCategoria'])->name('categoria.guardarCategoria');
        Route::post('/eliminarCategoria', [CategoriaProductoController::class, 'eliminarCategoria'])->name('categoria.eliminarCategoria');
    });

    // PRODUCTO
    Route::prefix('/producto')->group(function () {
        Route::get('/listado', [ProductoController::class, 'listado'])->name('producto.listado');
        Route::post('/ajaxListado', [ProductoController::class, 'ajaxListado'])->name('producto.ajaxListado');
        Route::post('/guardarProducto', [ProductoController::class, 'guardarProducto'])->name('producto.guardarProducto');
        Route::post('/eliminarProducto', [ProductoController::class, 'eliminarProducto'])->name('producto.eliminarProducto');

        Route::post('/guardarStockSucursal', [ProductoController::class, 'guardarStockSucursal'])->name('producto.guardarStockSucursal');
        Route::post('/ajaxStockSucursal', [ProductoController::class, 'ajaxStockSucursal'])->name('producto.ajaxStockSucursal');

        Route::post('/ajaxFormTransferencia', [ProductoController::class, 'ajaxFormTransferencia'])->name('producto.ajaxFormTransferencia');
        Route::post('/guardarTransferenciaSucursal', [ProductoController::class, 'guardarTransferenciaSucursal'])->name('producto.guardarTransferenciaSucursal');
        Route::post('/guardarSalidaSucursal', [ProductoController::class, 'guardarSalidaSucursal'])->name('producto.guardarSalidaSucursal');

        Route::get('/pdfProductoStock', [ProductoController::class, 'pdfProductoStock'])->name('producto.pdfProductoStock');

        Route::post('/generarReporteIngreso', [ProductoController::class, 'generarReporteIngreso'])->name('producto.generarReporteIngreso');
        Route::post('/generarReporteSalida', [ProductoController::class, 'generarReporteSalida'])->name('producto.generarReporteSalida');

        Route::post('/importarServiciosProductosExcel', [ProductoController::class, 'importarServiciosProductosExcel'])->name('producto.importarServiciosProductosExcel');
        Route::post('/ajax-productos-stock-masivo', [ProductoController::class, 'ajaxProductosStockMasivo'])->name('producto.ajaxProductosStockMasivo');
        Route::post('/guardar-stock-masivo', [ProductoController::class, 'guardarStockMasivo'])->name('producto.guardarStockMasivo');
    });

    // CLIENTE
    Route::prefix('/cliente')->group(function () {
        Route::get('/listado', [ClienteController::class, 'listado'])->name('cliente.listado');
        Route::post('/ajaxListado', [ClienteController::class, 'ajaxListado'])->name('cliente.ajaxListado');
        Route::post('/guardarCliente', [ClienteController::class, 'guardarCliente'])->name('cliente.guardarCliente');
        Route::post('/eliminarCliente', [ClienteController::class, 'eliminarCliente'])->name('cliente.eliminarCliente');
    });

    // CATEGORIA
    Route::prefix('/categoriaIngresoSalida')->group(function () {
        Route::get('/listado', [CategoriaIngresoSalidaController::class, 'listado'])->name('categoriaIngresoSalida.listado');
        Route::post('/ajaxListado', [CategoriaIngresoSalidaController::class, 'ajaxListado'])->name('categoriaIngresoSalida.ajaxListado');
        Route::post('/guardar', [CategoriaIngresoSalidaController::class, 'guardar'])->name('categoriaIngresoSalida.guardar');
        Route::post('/eliminar', [CategoriaIngresoSalidaController::class, 'eliminar'])->name('categoriaIngresoSalida.eliminar');
    });

    // CAJA
    Route::prefix('/caja')->group(function () {
        Route::get('/listado', [CajaController::class, 'listado'])->name('caja.listado');
        Route::post('/ajaxListado', [CajaController::class, 'ajaxListado']);
        Route::post('/guardarAperturaCaja', [CajaController::class, 'guardarAperturaCaja']);
        Route::post('/guardarCerrarCaja', [CajaController::class, 'guardarCerrarCaja']);
        Route::post('/formularioEdicionCaja', [CajaController::class, 'formularioEdicionCaja']);
        Route::post('/verCaja', [CajaController::class, 'verCaja']);
    });

    // FACTURA
    Route::prefix('/factura')->group(function () {
        Route::get('/formulario', [FacturaController::class, 'formulario'])->name('factura.formulario');
        Route::get('/listado', [FacturaController::class, 'listado'])->name('factura.listado');
        Route::post('/ajaxListadoFacturas', [FacturaController::class, 'ajaxListadoFacturas'])->name('factura.ajaxListadoFacturas');
        Route::post('/ajaxListadoClientesBusqueda', [FacturaController::class, 'ajaxListadoClientesBusqueda'])->name('factura.ajaxListadoClientesBusqueda');
        Route::post('/emitirRecibo', [FacturaController::class, 'emitirRecibo'])->name('factura.emitirRecibo');
        Route::post('/ajaxFormularioEditarVenta', [FacturaController::class, 'ajaxFormularioEditarVenta'])->name('factura.ajaxFormularioEditarVenta');
        Route::post('/guardarEdicionVenta', [FacturaController::class, 'guardarEdicionVenta'])->name('factura.guardarEdicionVenta');
        Route::get('/imprimeRecibo/{factura_id}', [FacturaController::class, 'imprimeRecibo']);
        Route::get('/imprimeReciboRollo/{factura_id}', [FacturaController::class, 'imprimeReciboRollo']);
        Route::post('/anularRecibo', [FacturaController::class, 'anularRecibo'])->name('factura.anularRecibo');
    });

    // PAGO
    Route::prefix('/pago')->group(function () {
        Route::post('/guardarTipoIngresoSalida', [PagoController::class, 'guardarTipoIngresoSalida']);
        Route::get('/listado', [PagoController::class, 'listado'])->name('pago.listado');
        Route::get('/listadoHermanos', [PagoController::class, 'listadoHermanos'])->name('pago.listadoHermanos');
        Route::post('/ajaxListadoHermanos', [PagoController::class, 'ajaxListadoHermanos'])->name('pago.ajaxListadoHermanos');
        Route::post('/ajaxListado', [PagoController::class, 'ajaxListado'])->name('pago.ajaxListado');
        Route::get('/listadoDeuda', [PagoController::class, 'listadoDeuda'])->name('pago.listadoDeuda');
        Route::post('/ajaxListadoDeuda', [PagoController::class, 'ajaxListadoDeuda'])->name('pago.ajaxListadoDeuda');
        Route::post('/ajaxFormPagoDeuda', [PagoController::class, 'ajaxFormPagoDeuda'])->name('pago.ajaxFormPagoDeuda');
        Route::post('/guardarPagoDeuda', [PagoController::class, 'guardarPagoDeuda'])->name('pago.guardarPagoDeuda');
        Route::post('/ajaxDescargarReportePago', [PagoController::class, 'ajaxDescargarReportePago'])->name('pago.ajaxDescargarReportePago');
        Route::post('/eliminarPago', [PagoController::class, 'eliminarPago'])->name('pago.eliminarPago');
        Route::get('/comprobantePago/{pago_id}', [PagoController::class, 'comprobantePago'])->name('pago.comprobantePago');
    });

    Route::prefix('/reporte')->group(function () {

        Route::get('/listado', [ReporteController::class, 'listado'])->name('reporte.listado');

        // ==========================
        // VENTAS
        // ==========================
        Route::get('/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('reporte.ventas.pdf');
        Route::get('/ventas/excel', [ReporteController::class, 'ventasExcel'])->name('reporte.ventas.excel');

        // ==========================
        // CUENTAS POR COBRAR
        // ==========================
        Route::get('/cuentas-cobrar/pdf', [ReporteController::class, 'cuentasCobrarPdf'])->name('reporte.cuentasCobrar.pdf');
        Route::get('/cuentas-cobrar/excel', [ReporteController::class, 'cuentasCobrarExcel'])->name('reporte.cuentasCobrar.excel');

        // ==========================
        // INVENTARIO
        // ==========================
        Route::get('/inventario/pdf', [ReporteController::class, 'inventarioPdf'])->name('reporte.inventario.pdf');
        Route::get('/inventario/excel', [ReporteController::class, 'inventarioExcel'])->name('reporte.inventario.excel');

        // ==========================
        // KARDEX
        // ==========================
        Route::get('/kardex/pdf', [ReporteController::class, 'kardexPdf'])->name('reporte.kardex.pdf');
        Route::get('/kardex/excel', [ReporteController::class, 'kardexExcel'])->name('reporte.kardex.excel');

        // ==========================
        // FLUJO EFECTIVO
        // ==========================
        Route::get('/flujo-efectivo/pdf', [ReporteController::class, 'flujoEfectivoPdf'])->name('reporte.flujoEfectivo.pdf');
        Route::get('/flujo-efectivo/excel', [ReporteController::class, 'flujoEfectivoExcel'])->name('reporte.flujoEfectivo.excel');

        // ==========================
        // AGENDA
        // ==========================
        Route::get('/agenda/pdf', [ReporteController::class, 'agendaPdf'])->name('reporte.agenda.pdf');
        Route::get('/agenda/excel', [ReporteController::class, 'agendaExcel'])->name('reporte.agenda.excel');

        Route::get('/utilidades/pdf', [ReporteController::class, 'utilidadesPdf'])->name('reporte.utilidades.pdf');
        Route::get('/utilidades/excel', [ReporteController::class, 'utilidadesExcel'])->name('reporte.utilidades.excel');
    });
});
