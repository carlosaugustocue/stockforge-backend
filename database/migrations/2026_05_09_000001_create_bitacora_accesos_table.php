<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: tabla bitacora_accesos
 *
 * Registra todos los eventos de autenticación del sistema.
 * Es una tabla INMUTABLE: no tiene updated_at y nunca se borran registros.
 * Esto cumple el requisito RNF-MAN del proyecto (trazabilidad de accesos).
 *
 * Eventos registrados: login_exitoso, login_fallido, logout, cuenta_bloqueada
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora_accesos', function (Blueprint $table) {
            $table->bigIncrements('id');

            // user_id nullable porque un login fallido puede no tener usuario en BD
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Tipo de evento registrado
            $table->string('accion');

            // IP del cliente — hasta 45 caracteres para soportar IPv6
            $table->string('ip_address', 45);

            // Información del navegador/cliente para auditoría
            $table->string('user_agent', 500)->nullable();

            // Solo created_at — la bitácora es inmutable (RNF-MAN)
            // No tiene updated_at porque un registro de auditoría jamás se modifica
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_accesos');
    }
};
