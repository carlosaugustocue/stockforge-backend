<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * LoginRequest — Validación de entrada para el inicio de sesión
 *
 * Los FormRequests centralizan la validación de datos de entrada.
 * Esto cumple SOLID-SRP: el controller no valida, solo delega y procesa.
 */
class LoginRequest extends FormRequest
{
    /**
     * Todos los usuarios pueden intentar hacer login (ruta pública).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para el login.
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    /**
     * Mensajes de error en español.
     * Laravel los mostrará automáticamente cuando una regla no se cumpla.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'El correo electrónico no tiene un formato válido.',
            'email.max'         => 'El correo electrónico no puede superar los 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string'   => 'La contraseña debe ser una cadena de texto.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
