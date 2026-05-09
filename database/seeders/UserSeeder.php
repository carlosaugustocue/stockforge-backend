<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * UserSeeder — Crea un usuario de prueba por cada rol del sistema.
 *
 * Estos usuarios se usan para pruebas en desarrollo y para los tests de Pest.
 * Las contraseñas están hasheadas con bcrypt (RNFSEC-01).
 *
 * IMPORTANTE: Cambiar estas credenciales en producción.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener los roles creados por RoleSeeder
        $roles = Role::pluck('id', 'nombre');

        $usuarios = [
            [
                'name'               => 'Administrador',
                'email'              => 'admin@inventario.test',
                'password'           => bcrypt('Admin1234!'),
                'role_id'            => $roles[Role::ADMINISTRADOR],
                'activo'             => true,
                'intentos_fallidos'  => 0,
            ],
            [
                'name'               => 'Gerencia',
                'email'              => 'gerencia@inventario.test',
                'password'           => bcrypt('Gerencia1234!'),
                'role_id'            => $roles[Role::GERENCIA],
                'activo'             => true,
                'intentos_fallidos'  => 0,
            ],
            [
                'name'               => 'Jefe Producción',
                'email'              => 'produccion@inventario.test',
                'password'           => bcrypt('Prod1234!'),
                'role_id'            => $roles[Role::JEFE_PRODUCCION],
                'activo'             => true,
                'intentos_fallidos'  => 0,
            ],
            [
                'name'               => 'Encargado Inventarios',
                'email'              => 'inventarios@inventario.test',
                'password'           => bcrypt('Inv1234!'),
                'role_id'            => $roles[Role::ENCARGADO_INVENTARIOS],
                'activo'             => true,
                'intentos_fallidos'  => 0,
            ],
        ];

        foreach ($usuarios as $usuario) {
            // firstOrCreate evita duplicados en ejecuciones repetidas del seeder
            User::firstOrCreate(
                ['email' => $usuario['email']],
                $usuario
            );
        }
    }
}
