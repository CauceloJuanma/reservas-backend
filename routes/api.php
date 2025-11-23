<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProductoController;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to ReservaApp']);
});

Route::get('/companies', [EmpresaController::class, 'index']);
Route::get('/products/{id}', [ProductoController::class, 'productos_empresa']);

// ✅ Rutas de autenticación CON middleware web
Route::middleware(['web'])->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// ✅ Rutas protegidas - IMPORTANTE: también con middleware web
Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return response()->json($request->user());
    });
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});

Route::middleware(['web'])->get('/test-session', function (\Illuminate\Http\Request $request) {
    // Iniciar sesión manualmente
    $request->session()->put('test_key', 'test_value');
    $request->session()->save();
    
    return response()->json([
        'session_id' => $request->session()->getId(),
        'session_driver' => config('session.driver'),
        'session_cookie_name' => config('session.cookie'),
        'session_domain' => config('session.domain'),
        'session_path' => config('session.path'),
        'session_same_site' => config('session.same_site'),
        'session_data' => $request->session()->all(),
        'cookies_sent' => $request->cookies->all(),
    ]);
});
