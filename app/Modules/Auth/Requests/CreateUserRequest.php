<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CreateUserRequest — Validación de entrada para la creación de usuarios
 *
 * Solo el administrador puede crear usuarios (la restricción de rol se aplica
 * en el middleware 'role:administrador' de las rutas).
 * Este Request valida que los datos del nuevo usuario sean correctos.
 */
class CreateUserRequest extends FormRequest
{
    /**
     * La autorización por rol se delega al middleware CheckRole.
     * Aquí solo se validan los datos, no los permisos.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para la creación de usuario.
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'  => ['required', 'exists:roles,id'],
        ];
    }

    /**
     * Mensajes de validación en español.
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre del usuario es obligatorio.',
            'name.max'           => 'El nombre no puede superar los 255 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El correo electrónico no tiene un formato válido.',
            'email.unique'       => 'Ya existe un usuario registrado con ese correo electrónico.',
            'email.max'          => 'El correo electrónico no puede superar los 255 caracteres.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role_id.required'   => 'Debe asignar un rol al usuario.',
            'role_id.exists'     => 'El rol seleccionado no existe en el sistema.',
        ];
    }
}
