<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * RoleSeeder — Popula la tabla de roles con los valores iniciales del sistema.
 *
 * Se debe ejecutar ANTES de UserSeeder porque los usuarios necesitan role_id.
 * Los 4 roles corresponden al esquema RBAC del proyecto (RFAUT02).
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre'      => Role::ADMINISTRADOR,
                'descripcion' => 'Administrador del sistema, acceso total a todas las funcionalidades.',
            ],
            [
                'nombre'      => Role::GERENCIA,
                'descripcion' => 'Gerencia, visualización de reportes y configuración de parámetros del sistema.',
            ],
            [
                'nombre'      => Role::JEFE_PRODUCCION,
                'descripcion' => 'Jefe de Producción, operación productiva y gestión de despachos.',
            ],
            [
                'nombre'      => Role::ENCARGADO_INVENTARIOS,
                'descripcion' => 'Encargado de Inventarios, operación diaria completa del inventario.',
            ],
        ];

        foreach ($roles as $rol) {
            // firstOrCreate evita duplicados si el seeder se ejecuta más de una vez
            Role::firstOrCreate(['nombre' => $rol['nombre']], $rol);
        }
    }
}
