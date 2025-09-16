<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\cilindros;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/cilindros', [cilindros::class, 'index']);
Route::post('/cilindros', [cilindros::class, 'store']);
Route::get('/cilindros/{numero_serie}', [cilindros::class, 'show']);
Route::post('/cilindros/{numero}/qr', [cilindros::class, 'updateQr']);


Route::get('/dashboard/total',[cilindros::class, 'totalCilindros']);
Route::get('/dashboard/proximos-caducar',[cilindros::class, 'proxCaducidad']);
Route::get('/dashboard/registros-mensuales',[cilindros::class, 'RegistrosMensuales']);
Route::get('/dashboard/ultimos-registros',[cilindros::class, 'UltimosRegistros']);
Route::get('/dashboard/sin-ficha',[cilindros::class, 'sinFicha']);
