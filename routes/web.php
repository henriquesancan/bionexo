<?php

use App\Http\Controllers\SeleniumController;
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

Route::get('/pdf-csv', ['App\Http\Controllers\CSVController', 'gerarCsv']);

Route::get('/tabela', ['App\Http\Controllers\TabelaController', 'extracao']);

Route::controller(SeleniumController::class)->group(function () {
    Route::get('/download', 'testDownload');

    Route::get('/formulario', 'testPreencheFormulario');

    Route::get('/upload', 'testUpload');
});
