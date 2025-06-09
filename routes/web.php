<?php

use App\Http\Controllers\catalogo\CargoCatalogoController;
use App\Http\Controllers\catalogo\PrestamoCatalogoController;
use App\Http\Controllers\catalogo\ReciboCatalogoController;
use App\Http\Controllers\web\PersonaWebController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\web\CredencialesController;
use App\Http\Controllers\web\PrestamoFijoWebController;
use App\Http\Controllers\web\PrestamoWebController;
use App\Http\Controllers\web\ReciboFijoWebController;
use App\Http\Controllers\web\ReciboWebController;
use App\Http\Controllers\web\TipoPagoCatalogoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();
Route::get('/', [HomeController::class, 'index']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::resource('prestamo_web', PrestamoWebController::class);
Route::resource('prestamo_fijo_web', PrestamoFijoWebController::class);
Route::post('cargo_web', [ReciboWebController::class,'cargo_web']);
Route::resource('recibo_web', ReciboWebController::class);
Route::resource('recibo_fijo_web', ReciboFijoWebController::class);
Route::resource('persona_web', PersonaWebController::class);

Route::resource('credenciales_web', CredencialesController::class);

//catalogos
Route::resource('prestamo_catalogo', PrestamoCatalogoController::class);
Route::get('recibo_catalogo/create/{prestamo}', [ReciboCatalogoController::class,'create']);
Route::resource('recibo_catalogo', ReciboCatalogoController::class);
Route::resource('cargo_catalogo', CargoCatalogoController::class);

Route::resource('tipo_pago', TipoPagoCatalogoController::class);
