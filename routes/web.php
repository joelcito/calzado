<?php

use App\Http\Controllers\HomeController;
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
});
