<?php

use App\Http\Controllers\api\CargoController;
use App\Http\Controllers\api\CargoFijoController;
use App\Http\Controllers\api\PersonaController;
use App\Http\Controllers\api\PrestamoController;
use App\Http\Controllers\api\PrestamoFijoController;
use App\Http\Controllers\api\ReciboController;
use App\Http\Controllers\api\ReciboFijoController;
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
Route::resource('pagos', ReportesController::class);
Route::resource('cargo', CargoController::class);
Route::resource('prestamo_fijo', PrestamoFijoController::class);
Route::resource('recibo_fijo', ReciboFijoController::class);
Route::resource('cargo_fijo', CargoFijoController::class);
