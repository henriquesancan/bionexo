<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/extracao', ['App\Http\Controllers\TabelaController', 'extracao']);

Route::get('/pdf-csv', ['App\Http\Controllers\CSVController', 'gerarCsv']);
