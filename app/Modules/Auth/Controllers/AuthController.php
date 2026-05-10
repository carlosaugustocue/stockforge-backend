<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\CreateUserRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Resources\UserResource;
use App\Modules\Auth\Services\AuthService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AuthController — Controlador del módulo de autenticación
 *
 * SOLID-SRP: el controller solo orquesta — recibe la petición HTTP,
 * delega al Service y retorna la respuesta JSON.
 * No contiene lógica de negocio ni acceso directo a la base de datos.
 *
 * El trait ApiResponseTrait garantiza respuestas JSON consistentes en toda la API.
 */
class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Inyección de dependencia del AuthService.
     * El Service encapsula toda la lógica de negocio de autenticación.
     */
    public function __construct(
        private AuthService $authService
    ) {}

    // -----------------------------------------------------------------------
    // POST /api/auth/login
    // -----------------------------------------------------------------------

    /**
     * Procesa el inicio de sesión.
     * Ruta pública — no requiere token de autenticación.
     *
     * @return JsonResponse HTTP 200 con token | HTTP 401 si las credenciales fallan
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $resultado = $this->authService->login(
                $request->email,
                $request->password,
                $request
            );

            return $this->successResponse([
                'usuario' => new UserResource($resultado['usuario']),
                'token'   => $resultado['token'],
                'rol'     => $resultado['rol'],
            ], 'Inicio de sesión exitoso.');

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), (int) ($e->getCode() ?: 401));
        }
    }

    // -----------------------------------------------------------------------
    // POST /api/auth/logout
    // -----------------------------------------------------------------------

    /**
     * Cierra la sesión del usuario autenticado.
     * Ruta protegida — requiere token Sanctum válido.
     *
     * @return JsonResponse HTTP 200 con mensaje de confirmación
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user(), $request);
            return $this->successResponse(null, 'Sesión cerrada exitosamente.');

        } catch (\Exception $e) {
            return $this->errorResponse('Error al cerrar sesión.', 500);
        }
    }

    // -----------------------------------------------------------------------
    // GET /api/auth/me
    // -----------------------------------------------------------------------

    /**
     * Retorna los datos del usuario autenticado actualmente.
     * Ruta protegida — requiere token Sanctum válido.
     *
     * @return JsonResponse HTTP 200 con datos del usuario
     */
    public function me(Request $request): JsonResponse
    {
        // Cargar la relación 'role' para incluir el nombre del rol en la respuesta
        $user = $request->user()->load('role');

        return $this->successResponse(
            new UserResource($user),
            'Datos del usuario autenticado.'
        );
    }

    // -----------------------------------------------------------------------
    // POST /api/auth/usuarios
    // -----------------------------------------------------------------------

    /**
     * Crea un nuevo usuario en el sistema.
     * Ruta protegida + solo ADMINISTRADOR (middleware 'role:administrador').
     *
     * @return JsonResponse HTTP 201 con el usuario creado | HTTP 500 si hay error
     */
    public function crearUsuario(CreateUserRequest $request): JsonResponse
    {
        try {
            $usuario = $this->authService->crearUsuario($request->validated());

            return $this->createdResponse(
                new UserResource($usuario->load('role')),
                'Usuario creado exitosamente.'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear el usuario.', 500);
        }
    }
}
