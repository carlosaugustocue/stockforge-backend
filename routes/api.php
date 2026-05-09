<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Módulo de Autenticación
|--------------------------------------------------------------------------
|
| Estructura de rutas del módulo Auth.
| Todas las rutas retornan JSON y siguen el estándar REST.
|
| Rutas públicas:  sin autenticación (login)
| Rutas privadas:  requieren token Sanctum válido (auth:sanctum)
| Rutas de admin:  requieren además el rol 'administrador'
|
*/

// -------------------------------------------------------------------------
// Rutas PÚBLICAS — No requieren autenticación
// -------------------------------------------------------------------------
Route::prefix('auth')->group(function () {
    // POST /api/auth/login
    // Permite iniciar sesión y obtener un token de acceso
    Route::post('/login', [AuthController::class, 'login']);
});

// -------------------------------------------------------------------------
// Rutas PRIVADAS — Requieren token Sanctum válido
// -------------------------------------------------------------------------
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {

    // POST /api/auth/logout — Cierra la sesión del usuario autenticado
    Route::post('/logout', [AuthController::class, 'logout']);

    // GET /api/auth/me — Retorna los datos del usuario autenticado
    Route::get('/me', [AuthController::class, 'me']);

    // Rutas exclusivas del ADMINISTRADOR (RFAUT02 - RBAC)
    Route::middleware('role:administrador')->group(function () {
        // POST /api/auth/usuarios — Crea un nuevo usuario en el sistema
        Route::post('/usuarios', [AuthController::class, 'crearUsuario']);
    });
});
