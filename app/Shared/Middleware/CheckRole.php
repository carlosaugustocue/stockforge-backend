<?php

namespace App\Shared\Middleware;

use App\Shared\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware CheckRole — Control de acceso basado en roles (RBAC)
 *
 * Verifica que el usuario autenticado tenga uno de los roles permitidos
 * antes de dejar pasar la petición al controller.
 *
 * Se registra en bootstrap/app.php con el alias 'role'.
 * Uso en rutas: Route::middleware('role:administrador,gerencia')
 *
 * Si el usuario no tiene el rol requerido → HTTP 403 Forbidden.
 * Esto implementa el principio de menor privilegio (RNFSEC-04).
 */
class CheckRole
{
    use ApiResponseTrait;

    /**
     * Procesa la petición HTTP.
     *
     * @param  Request  $request       Petición entrante
     * @param  Closure  $next          Siguiente middleware/controller
     * @param  string   ...$roles      Roles permitidos (uno o más)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Verificar si el usuario tiene alguno de los roles permitidos
        foreach ($roles as $rol) {
            if ($user?->hasRole($rol)) {
                return $next($request); // Acceso concedido
            }
        }

        // Ningún rol coincide — acceso denegado
        return $this->errorResponse('No tienes permisos para realizar esta acción.', 403);
    }
}
