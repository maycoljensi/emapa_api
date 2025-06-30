<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ReciboController;
use App\Http\Controllers\API\ReciboDigitalController;
use App\Http\Controllers\Api\DeudaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/consulta_duplicado', [ReciboController::class, 'getReciboDuplicado']);

Route::post('/registrar_afiliacion', [ReciboDigitalController::class, 'registrarAfiliacion']);

Route::post('/consulta_deuda', [DeudaController::class, 'consultar']);

