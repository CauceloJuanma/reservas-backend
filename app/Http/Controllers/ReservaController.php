<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\LineaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{
    // Crear nueva reserva o agregar items a existente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|integer|exists:empresa,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|integer|exists:producto,id',
            'items.*.cantidad' => 'required|integer|min:1|max:1000',
            'items.*.fecha_reserva' => 'required|date|after_or_equal:today',
            'items.*.hora_reserva' => 'nullable|date_format:H:i',
        ]);

        Log::info("DEBUG - Items validados", [
            'items' => $validated['items'],
        ]);

        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $empresaId = $validated['empresa_id'];

            // Validar stock para TODOS los items primero
            foreach ($validated['items'] as $item) {
                $product = Producto::findOrFail($item['producto_id']);

                if ($product->stock < $item['cantidad']) {
                    throw new \Exception(
                        "Stock insuficiente para '{$product->nombre}'. " .
                        "Disponible: {$product->stock}, Solicitado: {$item['cantidad']}"
                    );
                }

                // Validar horario si el producto tiene restricción
                if ($product->tieneRestriccionHoraria()) {
                    if (empty($item['hora_reserva'])) {
                        throw new \Exception(
                            "El producto '{$product->nombre}' requiere una hora de reserva. " .
                            "Horario disponible: {$product->getHoraIniFormato()} - {$product->getHoraFinFormato()}"
                        );
                    }

                    if (!$product->esHoraValida($item['hora_reserva'])) {
                        throw new \Exception(
                            "Hora inválida para '{$product->nombre}'. " .
                            "Debe estar entre {$product->getHoraIniFormato()} y {$product->getHoraFinFormato()}"
                        );
                    }
                }
            }

            // Obtener o crear reserva pendiente
            $totalImporte = 0;
            $fechaHora = null;

            foreach ($validated['items'] as $item) {
                $product = Producto::findOrFail($item['producto_id']);
                $totalImporte += $product->precio * $item['cantidad'];

                if (!empty($item['hora_reserva']) && $fechaHora === null) {
                    $fechaHora = $item['fecha_reserva'] . ' ' . $item['hora_reserva'];
                }
            }

            $reservation = Reserva::updateOrCreate(
                [
                    'usuario_id' => $userId,
                    'empresa_id' => $empresaId,
                    'estado_id' => 1, // Pendiente
                ],
                [
                    'importe' => $totalImporte,
                    'fecha_hora' => $fechaHora,
                ]
            );

            // Crear/actualizar líneas de producto
            $itemsCreated = 0;
            foreach ($validated['items'] as $item) {
                $product = Producto::findOrFail($item['producto_id']);

                LineaProducto::updateOrCreate(
                    [
                        'reserva_id' => $reservation->id,
                        'producto_id' => $item['producto_id'],
                    ],
                    [
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $product->precio,
                        'subtotal' => $product->precio * $item['cantidad'],
                    ]
                );

                $itemsCreated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada/actualizada correctamente',
                'reservation_id' => $reservation->id,
                'items_count' => $itemsCreated,
                'total' => $reservation->lineas->sum('subtotal'),
                'reservation' => $reservation->load('lineas.producto'),
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación inválidos',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Error al crear reserva", [
                'error' => $e->getMessage(),
                'usuario_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

   
    // Ver detalles de reserva
    public function show($id)
    {
        try {
            $userId = auth()->id();

            $reservation = Reserva::with('lineas.producto', 'empresa', 'estado')
                ->findOrFail($id);

            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver esta reserva'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'reservation' => $reservation,
                'total' => $reservation->lineas->sum('subtotal'),
                'items_count' => $reservation->lineas->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al obtener reserva", [
                'error' => $e->getMessage(),
                'reserva_id' => $id,
                'usuario_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Listar todas las reservas del usuario autenticado
    public function index()
    {
        try {
            $userId = auth()->id();

            $reservas = Reserva::where('usuario_id', $userId)
                ->with([
                    'lineas.producto',
                    'empresa',
                    'estado'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            $reservasFormateadas = [];
            foreach ($reservas as $reserva) {
                $primerProducto = $reserva->lineas->first()?->producto?->nombre ?? 'N/A';

                $reservasFormateadas[] = [
                    'id' => $reserva->id,
                    'empresa' => $reserva->empresa?->nombre ?? 'N/A',
                    'producto' => $primerProducto,
                    'estado' => $reserva->estado?->nombre ?? 'N/A',
                    'estado_id' => $reserva->estado_id,
                    'items_count' => $reserva->lineas->count(),
                    'total' => $reserva->lineas->sum('subtotal'),
                    'fecha' => $reserva->created_at->format('d/m/Y H:i'),
                    'reserva' => $reserva,
                ];
            }

            return response()->json([
                'success' => true,
                'reservas' => $reservasFormateadas,
                'count' => count($reservasFormateadas),
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al listar reservas", [
                'error' => $e->getMessage(),
                'usuario_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Confirmar reserva (reduce stock y cambia estado)
    public function confirm(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $reservation = Reserva::findOrFail($id);

            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para confirmar esta reserva'
                ], 403);
            }

            if ($reservation->estado_id !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reserva no puede ser confirmada'
                ], 422);
            }

            // Validar stock nuevamente
            foreach ($reservation->lineas as $linea) {
                if ($linea->producto->stock < $linea->cantidad) {
                    throw new \Exception(
                        "Stock insuficiente para '{$linea->producto->nombre}'. " .
                        "Disponible: {$linea->producto->stock}, Solicitado: {$linea->cantidad}"
                    );
                }
            }

            // Reducir stock
            foreach ($reservation->lineas as $linea) {
                $linea->producto->decrement('stock', $linea->cantidad);
            }

            // Cambiar estado a confirmada
            $reservation->update(['estado_id' => 2]);

            DB::commit();

            Log::info("Reserva confirmada", [
                'reserva_id' => $id,
                'usuario_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva confirmada correctamente',
                'reservation' => $reservation->load('lineas.producto'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error al confirmar reserva", [
                'error' => $e->getMessage(),
                'reserva_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Cancelar reserva (restaura stock)
    public function cancel(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $reservation = Reserva::with('lineas.producto')->findOrFail($id);

            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para cancelar esta reserva'
                ], 403);
            }

            if ($reservation->estado_id === 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reserva ya está cancelada'
                ], 422);
            }

            // No cancelar si ya pasó la fecha/hora
            if ($reservation->fecha_hora) {
                $fechaReserva = \Carbon\Carbon::parse($reservation->fecha_hora);
                if ($fechaReserva->isPast()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puedes cancelar reservas que ya han pasado su fecha/hora'
                    ], 422);
                }
            }

            // Si fue confirmada, restaurar stock
            if ($reservation->estado_id === 2) {
                foreach ($reservation->lineas as $linea) {
                    $linea->producto->increment('stock', $linea->cantidad);
                }
            }

            // Cambiar estado a cancelada
            $reservation->update(['estado_id' => 3]);

            DB::commit();

            Log::info("Reserva cancelada", [
                'reserva_id' => $id,
                'usuario_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada correctamente',
                'reservation' => $reservation,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error al cancelar reserva", [
                'error' => $e->getMessage(),
                'reserva_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Obtener slots disponibles para un producto
    public function getProductSlots($productoId)
    {
        try {
            $product = Producto::findOrFail($productoId);

            if (!$product->tieneRestriccionHoraria()) {
                return response()->json([
                    'success' => true,
                    'hasTimeRestriction' => false,
                    'slots' => [],
                    'message' => 'Este producto no tiene restricción horaria',
                ]);
            }

            $slots = $product->generarSlots(30);

            return response()->json([
                'success' => true,
                'hasTimeRestriction' => true,
                'slots' => $slots,
                'hora_ini' => $product->getHoraIniFormato(),
                'hora_fin' => $product->getHoraFinFormato(),
            ]);

        } catch (\Exception $e) {
            Log::error("Error al obtener slots", [
                'error' => $e->getMessage(),
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}