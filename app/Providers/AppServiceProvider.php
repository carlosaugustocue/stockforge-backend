<?php

namespace App\Providers;

use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los bindings del contenedor de dependencias.
     *
     * Aquí se aplica SOLID-DIP (Dependency Inversion Principle):
     * Se enlaza la interfaz con su implementación concreta.
     * Cuando el framework inyecte UserRepositoryInterface en cualquier clase,
     * entregará automáticamente una instancia de UserRepository.
     *
     * Si en el futuro se cambia la BD (ej. MongoDB), solo se cambia esta línea.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
