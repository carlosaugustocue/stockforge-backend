<?php

namespace App\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource — DTO pattern para la serialización del usuario
 *
 * Controla exactamente qué datos del usuario viajan al cliente.
 * Nunca expone: password, intentos_fallidos, remember_token, tokens.
 *
 * DTO pattern — controla qué datos viajan al cliente (RNF-SEC).
 * Al usar un Resource en lugar de serializar el modelo directamente,
 * garantizamos que no se filtre información sensible aunque se agreguen
 * nuevos campos al modelo en el futuro.
 */
class UserResource extends JsonResource
{
    /**
     * Transforma el recurso en un array serializable a JSON.
     * Solo se incluyen los campos que el frontend necesita.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nombre'     => $this->name,
            'email'      => $this->email,
            'activo'     => $this->activo,
            'rol'        => $this->role?->nombre,       // Solo el nombre, no el objeto completo
            'creado_en'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
