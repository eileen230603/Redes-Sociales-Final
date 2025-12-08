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

            // VALIDACIÓN GENERAL
            $validator = Validator::make($request->all(), [
                'tipo_usuario'       => 'required|in:ONG,Empresa,Integrante externo',
                'nombre_usuario'     => 'required|string|max:50|unique:usuarios,nombre_usuario',
                'correo_electronico' => 'required|email|max:100|unique:usuarios,correo_electronico',
                'contrasena'         => 'required|string|min:6',

                // EXTERNO
                'nombres'            => 'required_if:tipo_usuario,Integrante externo|string|max:100',
                'apellidos'          => 'required_if:tipo_usuario,Integrante externo|string|max:100',

                // ONG
                'nombre_ong'         => 'required_if:tipo_usuario,ONG|string|max:100',
                'NIT'                => 'nullable|string|max:20',
                'telefono'           => 'nullable|string|max:20',
                'direccion'          => 'nullable|string|max:150',
                'sitio_web'          => 'nullable|string|max:150',
                'descripcion'        => 'nullable|string|max:500',

                // Empresa
                'nombre_empresa'     => 'required_if:tipo_usuario,Empresa|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                ], 422);
            }

            // CREAR USUARIO BASE
            $user = User::create([
                'nombre_usuario'     => $request->nombre_usuario,
                'correo_electronico' => $request->correo_electronico,
                'contrasena'         => Hash::make($request->contrasena),
                'tipo_usuario'       => $request->tipo_usuario,
                'activo'             => true,
            ]);

            // ---------------------------------------------
            // REGISTRO SEGÚN TIPO
            // ---------------------------------------------

            // ONG
            if ($user->tipo_usuario === "ONG") {
                Ong::create([
                    'user_id'     => $user->id_usuario,
                    'nombre_ong'  => $request->nombre_ong,
                    'NIT'         => $request->NIT,
                    'telefono'    => $request->telefono,
                    'direccion'   => $request->direccion,
                    'sitio_web'   => $request->sitio_web,
                    'descripcion' => $request->descripcion,
                ]);
            }

            // Empresa
            if ($user->tipo_usuario === "Empresa") {
                Empresa::create([
                    'user_id'        => $user->id_usuario,
                    'nombre_empresa' => $request->nombre_empresa,
                    'NIT'            => $request->NIT,
                    'telefono'       => $request->telefono,
                    'direccion'      => $request->direccion,
                    'sitio_web'      => $request->sitio_web,
                    'descripcion'    => $request->descripcion,
                ]);
            }

            // Integrante externo
            if ($user->tipo_usuario === "Integrante externo") {
                IntegranteExterno::create([
                    'user_id'          => $user->id_usuario,
                    'nombres'          => $request->nombres,
                    'apellidos'        => $request->apellidos,
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'email'            => $request->correo_electronico,
                    'phone_number'     => $request->telefono,
                    'descripcion'      => $request->descripcion,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente',
                'token'   => $token,
                'user'    => [
                    'id_usuario'     => $user->id_usuario,
                    'nombre_usuario' => $user->nombre_usuario,
                    'tipo_usuario'   => $user->tipo_usuario,
                ],
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    // LOGIN
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'correo_electronico' => 'required|email',
                'contrasena'         => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => 'Datos inválidos'], 422);
            }

            $user = User::where('correo_electronico', $request->correo_electronico)->first();

            if (!$user)
                return response()->json(['success' => false, 'error' => 'El usuario no existe'], 404);

            if (!Hash::check($request->contrasena, $user->contrasena))
                return response()->json(['success' => false, 'error' => 'Contraseña incorrecta'], 401);

            if (!$user->activo)
                return response()->json(['success' => false, 'error' => 'Cuenta inactiva'], 403);

            $token = $user->createToken('auth_token')->plainTextToken;

            // Para ONG y Empresa, id_entidad es el mismo que id_usuario
            // porque las tablas usan user_id como clave primaria
            $id_entidad = $user->id_usuario;
            
            // Si es Integrante externo, también es el mismo
            // (todos usan user_id como clave primaria en sus tablas relacionadas)

            return response()->json([
                'success' => true,
                'message' => 'Inicio correcto',
                'token'   => $token,
                'user'    => [
                    'id_usuario'     => $user->id_usuario,
                    'nombre_usuario' => $user->nombre_usuario,
                    'tipo_usuario'   => $user->tipo_usuario,
                    'id_entidad'     => $id_entidad,
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    // LOGOUT
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cerrar sesión: ' . $e->getMessage(),
            ], 500);
        }
    }
}
