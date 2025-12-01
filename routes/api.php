<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReservaController;

// Pública
Route::get('/', fn () => response()->json(['message' => 'Welcome to ReservaApp']));
Route::get('/companies', [EmpresaController::class, 'index']);
Route::get('/products/{id}', [ProductoController::class, 'productos_empresa']);
Route::get('/products/{id}/reserve', [ProductoController::class, 'reservar_producto']);

// Auth 
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Protegidas solo con auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->group(function () {
    // Crear reserva
    Route::post('/reservations', [ReservaController::class, 'store']);

    // Listar reservas del usuario
    Route::get('/reservations', [ReservaController::class, 'index']);

    // Ver detalle de reserva
    Route::get('/reservations/{id}', [ReservaController::class, 'show']);

    // Confirmar reserva
    Route::post('/reservations/{id}/confirm', [ReservaController::class, 'confirm']);

    // Cancelar reserva
    Route::post('/reservations/{id}/cancel', [ReservaController::class, 'cancel']);

    // Actualizar cantidad de item
    Route::patch('/reservations/{reservaId}/items/{itemId}', 
        [ReservaController::class, 'updateItem']);

    // Eliminar item
    Route::delete('/reservations/{reservaId}/items/{itemId}', 
        [ReservaController::class, 'removeItem']);
});