<?php

use App\Http\Controllers\api\PersonaController;
use App\Http\Controllers\api\PrestamoController;
use App\Http\Controllers\api\ReciboController;
use App\Http\Controllers\api\ReportesController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('login', [AuthController::class,'login']);
Route::resource('persona', PersonaController::class);
Route::resource('prestamo', PrestamoController::class);
Route::resource('recibo', ReciboController::class);
Route::resource('reportes', ReportesController::class);
