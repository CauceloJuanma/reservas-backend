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
    /**
     * 📝 Crear nueva reserva o agregar items a existente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|integer|exists:empresa,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|integer|exists:producto,id',
            'items.*.cantidad' => 'required|integer|min:1|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $empresaId = $validated['empresa_id'];

            // ✅ PASO 1: Validar stock para TODOS los items primero
            foreach ($validated['items'] as $item) {
                $product = Producto::findOrFail($item['producto_id']);

                if ($product->stock < $item['cantidad']) {
                    throw new \Exception(
                        "Stock insuficiente para '{$product->nombre}'. " .
                        "Disponible: {$product->stock}, Solicitado: {$item['cantidad']}"
                    );
                }
            }

            // ✅ PASO 2: Obtener o crear reserva pendiente
            $reservation = Reserva::firstOrCreate(
                [
                    'usuario_id' => $userId,
                    'empresa_id' => $empresaId,
                    'estado_id' => 1, // Pendiente
                ]
            );

            // ✅ PASO 3: Crear/actualizar líneas de producto
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

            Log::info("Reserva creada", [
                'reserva_id' => $reservation->id,
                'usuario_id' => $userId,
                'empresa_id' => $empresaId,
                'items' => $itemsCreated,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada/actualizada correctamente',
                'reservation_id' => $reservation->id,
                'items_count' => $itemsCreated,
                'total' => $this->calculateTotal($reservation),
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

    /**
     * 🔄 Actualizar cantidad de item en reserva
     */
    public function updateItem(Request $request, $reservaId, $itemId)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1|max:1000',
        ]);

        try {
            $userId = auth()->id();

            // ✅ Verificar que la reserva existe y pertenece al usuario
            $reservation = Reserva::findOrFail($reservaId);
            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para modificar esta reserva'
                ], 403);
            }

            // ✅ Verificar que el item existe y pertenece a la reserva
            $item = LineaProducto::findOrFail($itemId);
            if ($item->reserva_id !== $reservation->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'El item no pertenece a esta reserva'
                ], 403);
            }

            // ✅ Validar stock
            if ($item->producto->stock < $validated['cantidad']) {
                throw new \Exception(
                    "Stock insuficiente para '{$item->producto->nombre}'. " .
                    "Disponible: {$item->producto->stock}"
                );
            }

            // ✅ Actualizar item
            $item->update([
                'cantidad' => $validated['cantidad'],
                'subtotal' => $item->precio_unitario * $validated['cantidad'],
            ]);

            Log::info("Item actualizado", [
                'item_id' => $itemId,
                'reserva_id' => $reservaId,
                'nueva_cantidad' => $validated['cantidad'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item actualizado correctamente',
                'item' => $item,
                'total' => $this->calculateTotal($reservation),
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al actualizar item", [
                'error' => $e->getMessage(),
                'item_id' => $itemId,
                'usuario_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * ❌ Eliminar item de reserva
     */
    public function removeItem($reservaId, $itemId)
    {
        try {
            $userId = auth()->id();

            // ✅ Verificar pertenencia
            $reservation = Reserva::findOrFail($reservaId);
            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para modificar esta reserva'
                ], 403);
            }

            $item = LineaProducto::findOrFail($itemId);
            if ($item->reserva_id !== $reservation->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'El item no pertenece a esta reserva'
                ], 403);
            }

            // ✅ Eliminar
            $item->delete();

            Log::info("Item eliminado", [
                'item_id' => $itemId,
                'reserva_id' => $reservaId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado correctamente',
                'total' => $this->calculateTotal($reservation),
                'items_count' => $reservation->lineas()->count() - 1,
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al eliminar item", [
                'error' => $e->getMessage(),
                'item_id' => $itemId,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * 📊 Ver detalles de reserva
     */
    public function show($id)
    {
        try {
            $userId = auth()->id();

            // ✅ Cargar con relaciones correctas
            $reservation = Reserva::with('lineas.producto', 'empresa', 'estado')
                ->findOrFail($id);

            // ✅ Verificar que pertenece al usuario
            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver esta reserva'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'reservation' => $reservation,
                'total' => $this->calculateTotal($reservation),
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

    /**
     * 📋 Listar todas las reservas del usuario autenticado
     */
    public function index()
    {
        try {
            $userId = auth()->id();

            $reservas = Reserva::where('usuario_id', $userId)
                ->with('lineas', 'empresa', 'estado')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($reserva) {
                    return [
                        'id' => $reserva->id,
                        'empresa' => $reserva->empresa->nombre ?? 'N/A',
                        'estado' => $reserva->estado->nombre ?? 'N/A',
                        'items_count' => $reserva->lineas->count(),
                        'total' => $this->calculateTotal($reserva),
                        'fecha' => $reserva->created_at->format('d/m/Y H:i'),
                        'reserva' => $reserva,
                    ];
                });

            return response()->json([
                'success' => true,
                'reservas' => $reservas,
                'count' => $reservas->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * 🧮 Calcular total de una reserva
     */
    private function calculateTotal(Reserva $reservation)
    {
        return $reservation->lineas->sum('subtotal');
    }

    /**
     * ✅ Confirmar reserva (reduce stock y cambia estado)
     */
    public function confirm(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $reservation = Reserva::findOrFail($id);

            // Verificar pertenencia
            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para confirmar esta reserva'
                ], 403);
            }

            // Verificar que está en estado pendiente
            if ($reservation->estado_id !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reserva no puede ser confirmada'
                ], 422);
            }

            // ✅ Validar stock nuevamente
            foreach ($reservation->lineas as $linea) {
                if ($linea->producto->stock < $linea->cantidad) {
                    throw new \Exception(
                        "Stock insuficiente para '{$linea->producto->nombre}'. " .
                        "Disponible: {$linea->producto->stock}, Solicitado: {$linea->cantidad}"
                    );
                }
            }

            // ✅ Reducir stock
            foreach ($reservation->lineas as $linea) {
                $linea->producto->decrement('stock', $linea->cantidad);
            }

            // ✅ Cambiar estado a confirmada (asume que estado_id 2 es confirmada)
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

    /**
     * ❌ Cancelar reserva (restaura stock)
     */
    public function cancel(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $reservation = Reserva::findOrFail($id);

            // Verificar pertenencia
            if ($reservation->usuario_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para cancelar esta reserva'
                ], 403);
            }

            // Verificar que no esté ya cancelada
            if ($reservation->estado_id === 3) { // 3 = cancelada
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reserva ya está cancelada'
                ], 422);
            }

            // ✅ Si fue confirmada, restaurar stock
            if ($reservation->estado_id === 2) { // 2 = confirmada
                foreach ($reservation->lineas as $linea) {
                    $linea->producto->increment('stock', $linea->cantidad);
                }
            }

            // ✅ Cambiar estado a cancelada
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
}
