<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProductoController;

// Pública
Route::get('/', fn () => response()->json(['message' => 'Welcome to ReservaApp']));
Route::get('/companies', [EmpresaController::class, 'index']);
Route::get('/products/{id}', [ProductoController::class, 'productos_empresa']);

// Auth (SIN 'web')
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Protegidas solo con auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});
