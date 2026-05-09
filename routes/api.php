<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Enrutador de versiones
|--------------------------------------------------------------------------
|
| Este archivo actúa como enrutador central de versiones de la API.
| Cada versión tiene su propio archivo de rutas aislado.
|
| v1 → routes/api_v1.php  (versión actual)
|
| Para agregar una nueva versión:
|   Route::prefix('v2')->group(base_path('routes/api_v2.php'));
|
*/

Route::prefix('v1')->group(base_path('routes/api_v1.php'));
