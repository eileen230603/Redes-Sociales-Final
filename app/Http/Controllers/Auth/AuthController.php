<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Ong;
use App\Models\Empresa;
use App\Models\IntegranteExterno;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'tipo_usuario' => 'required|in:ONG,Empresa,Integrante externo',
                'nombre_usuario' => 'required|string|max:50|unique:usuarios,nombre_usuario',
                'correo_electronico' => 'required|email|max:100|unique:usuarios,correo_electronico',
                'contrasena' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                ], 422);
            }

            $user = User::create([
                'nombre_usuario' => $request->nombre_usuario,
                'correo_electronico' => $request->correo_electronico,
                'contrasena' => Hash::make($request->contrasena),
                'tipo_usuario' => $request->tipo_usuario,
                'activo' => true,
            ]);

            if ($user->tipo_usuario === "ONG") {
                Ong::create([
                    'user_id' => $user->id_usuario
                ]);
            }

            if ($user->tipo_usuario === "Empresa") {
                Empresa::create([
                    'user_id' => $user->id_usuario
                ]);
            }

            if ($user->tipo_usuario === "Integrante externo") {
                IntegranteExterno::create([
                    'user_id' => $user->id_usuario
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente',
                'token' => $token,
                'user' => [
                    'id_usuario' => $user->id_usuario,
                    'nombre_usuario' => $user->nombre_usuario,
                    'tipo_usuario' => $user->tipo_usuario,
                ],
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'correo_electronico' => 'required|email',
                'contrasena' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => 'Datos inválidos'], 422);
            }

            $user = User::where('correo_electronico', $request->correo_electronico)->first();

            if (!$user) return response()->json(['success' => false, 'error' => 'El usuario no existe'], 404);

            if (!Hash::check($request->contrasena, $user->contrasena))
                return response()->json(['success' => false, 'error' => 'Contraseña incorrecta'], 401);

            if (!$user->activo)
                return response()->json(['success' => false, 'error' => 'Cuenta inactiva'], 403);

            $token = $user->createToken('auth_token')->plainTextToken;

            // LA ONG REAL → user_id
            $idEntidad = $user->id_usuario;

            return response()->json([
                'success' => true,
                'message' => 'Inicio correcto',
                'token' => $token,
                'user' => [
                    'id_usuario' => $user->id_usuario,
                    'id_entidad' => $idEntidad,   // ESTE ES EL QUE USARÁS
                    'nombre_usuario' => $user->nombre_usuario,
                    'tipo_usuario' => $user->tipo_usuario,
                ]
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }
}
