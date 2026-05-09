<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: tabla roles
 *
 * Se ejecuta ANTES que la migración de users porque la tabla users
 * tendrá una clave foránea (role_id) que referencia esta tabla.
 * El prefijo 0000_ garantiza que el orden de ejecución sea correcto.
 *
 * Roles del sistema (RFAUT02 - Control de acceso basado en roles RBAC):
 * - administrador
 * - gerencia
 * - jefe_produccion
 * - encargado_inventarios
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Nombre del rol — clave de negocio para el RBAC manual
            $table->string('nombre', 50)->unique();
            // Descripción legible para reportes e interfaz
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
