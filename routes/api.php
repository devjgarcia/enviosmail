<?php

use App\Http\Controllers\CorreosGruposController;
use App\Http\Controllers\CorreoInasistenciasController;
use App\Http\Controllers\CorreoLlegaTardeController;
use App\Http\Controllers\CorreoFaltaEmpleadoController;
use App\Http\Controllers\CorrsacaSMController;
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
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});


Route::middleware('api')->post('save_correo_conf', [CorrsacaSMController::class, 'saveCorrsacaSM']);
Route::middleware('api')->post('get_correo_conf', [CorrsacaSMController::class, 'getBySM']);

Route::get('correosgen_group', [CorreosGruposController::class, 'generarCorreo_Grupos']);
Route::get('correosgen_inasi', [CorreoInasistenciasController::class, 'armarCorreosInasi']);
Route::get('correosgen_llegatarde', [CorreoLlegaTardeController::class, 'armarCorreosLlegaTarde']);
Route::get('correosgen_llegatardeemple', [CorreoFaltaEmpleadoController::class, 'armarCorreo']);



Route::get('prueba', function() {
    echo "No eres bienvenido en esta ruta de sistema";
});