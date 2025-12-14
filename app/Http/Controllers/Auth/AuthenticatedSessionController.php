<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
            'pass' => ['required'],
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (! $usuario || ! Hash::check($request->pass, $usuario->pass)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Login con guard web
        Auth::guard('web')->login($usuario);
        
        // Regenerar sesión
        $request->session()->regenerate();
        
        // Guardar sesión inmediatamente
        $request->session()->save();
        
        // Actualizar user_id en la tabla sessions manualmente
        $sessionId = $request->session()->getId();
        DB::table('sessions')
            ->where('id', $sessionId)
            ->update(['user_id' => $usuario->id]);
        
        // Log para debug
        \Log::info('Login exitoso', [
            'session_id' => $sessionId,
            'user_id' => $usuario->id,
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'correo' => $usuario->correo,
                'tipo_id' => $usuario->tipo_id,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada']);
    }
}