<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//Agregamos nuestra ruta al controller de Transactions
Route::resource('transactions', 'TransactionsController');
Route::resource('pago', 'pagoExitosoController');
Route::resource('pagoFinal', 'pagoFinalController');
Route::post('pagoManual', 'pagoManualBonoController@generarVentaManual');
Route::post('zipBonosSorteo', 'pagoManualBonoController@zipBonosSorteo');
//Descarga de bonos segun email y oc
Route::post('descargarPorOC', 'pagoManualBonoController@descargarBonosPorOC');
// Se usa por postman en caso de emergencia para generar un bono de forma manual
Route::post('generarBonoAux', 'pagoManualBonoController@bonoAux');
// Generacion de Alcancias
Route::post('generaAlcancias', 'alcanciasController@generaAlcancias');
// Generación de Talonarios
Route::post('generaTalonarios', 'talonariosController@generaTalonarios');

//Consultamos todos los Bonos vendidos a traves de Transbank
Route::get('obtieneBonosVendidos', 'talonariosController@obtieneBonosVendidos');
//Bonos vendidos de forma Manual
Route::get('obtieneBonosManuales', 'talonariosController@obtieneBonosManuales');
