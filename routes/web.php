<?php

use App\Http\Controllers\web\PrestamoWebController;
use App\Http\Controllers\web\ReciboWebController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::resource('prestamo_web', PrestamoWebController::class);
Route::resource('recibo_web', ReciboWebController::class);
