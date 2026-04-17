<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required|string',
            'contra' => 'required|string',
        ]);

        $usuario = Usuario::where('usuario_usuario', $credentials['usuario'])->first();

        if ($usuario && Hash::check($credentials['contra'], $usuario->password_usuario)) {
            $token = $usuario->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Inicio de sesión exitoso',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre_usuario,
                    'rol' => $usuario->rol_usuario,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Credenciales incorrectas o el usuario no existe'
        ], 401);
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario_usuario',
            'contra' => 'required|string|min:6',
        ]);

        $usuario = Usuario::create([
            'nombre_usuario' => $request->nombre,
            'apellido_usuario' => $request->apellido,
            'usuario_usuario' => $request->usuario,
            'password_usuario' => Hash::make($request->contra),
            'estado_usuario' => 'activo',
            'rol_usuario' => 2, // Cliente por defecto
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registro exitoso',
            'data' => $usuario
        ]);
    }

    public function cerrarSesion()
    {
        // En una API con tokens, aquí revocaríamos el token
        return response()->json(['status' => 'success', 'message' => 'Sesión cerrada exitosamente']);
    }
}
