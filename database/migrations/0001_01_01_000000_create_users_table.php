<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // --- Campos adicionales del proyecto ---

            // Rol del usuario — clave foránea al RBAC manual (RFAUT02)
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();

            // Permite desactivar cuentas sin borrarlas del historial (RFAUT04)
            $table->boolean('activo')->default(true);

            // Contador de intentos fallidos para bloqueo automático tras 5 intentos (RFAUT01)
            $table->integer('intentos_fallidos')->default(0);

            // Fecha hasta la cual la cuenta permanece bloqueada (RFAUT01)
            $table->timestamp('bloqueado_hasta')->nullable();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
