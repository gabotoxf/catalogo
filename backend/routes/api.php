<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrincipalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas Públicas
Route::get('/home', [HomeController::class, 'index']);
Route::get('/categorias', [PrincipalController::class, 'categorias']);
Route::get('/productos', [PrincipalController::class, 'productos']);
Route::post('/productos/filtrar', [PrincipalController::class, 'filtrarProductos']);
Route::post('/productos/categoria', [PrincipalController::class, 'filtrarPorCategoria']);

// Autenticación
Route::post('/login', [AdminController::class, 'login']);
Route::post('/registrar', [AdminController::class, 'registrar']);
Route::post('/logout', [AdminController::class, 'cerrarSesion']);

// Rutas de Dashboard (Administración)
// En una fase posterior se puede añadir middleware auth:sanctum
Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    
    // Categorías
    Route::get('/categorias', [DashboardController::class, 'getCategorias']);
    Route::post('/categorias', [DashboardController::class, 'agregarCategoria']);
    Route::post('/categorias/{id}', [DashboardController::class, 'editarCategoria']);
    Route::delete('/categorias/{id}', [DashboardController::class, 'eliminarCategoria']);
    
    // Productos
    Route::get('/productos', [DashboardController::class, 'getProductos']);
    Route::post('/productos', [DashboardController::class, 'agregarProducto']);
    Route::post('/productos/{id}', [DashboardController::class, 'editarProducto']);
    Route::delete('/productos/{id}', [DashboardController::class, 'eliminarProducto']);

    // Usuarios
    Route::get('/usuarios', [DashboardController::class, 'getUsuarios']);
    Route::post('/usuarios', [DashboardController::class, 'agregarUsuario']);
    Route::post('/usuarios/{id}', [DashboardController::class, 'editarUsuario']);
    Route::delete('/usuarios/{id}', [DashboardController::class, 'eliminarUsuario']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
