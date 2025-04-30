<?php

use App\Http\Controllers\ControllerImpressoes;
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

Route::get('imprimir/visitaTecnica/{id}',[ControllerImpressoes::class, 'imprimirVisitaTecnica'])->name('imprimirVisitaTecnica');
Route::get('imprimir/relatorioFinal/{id}',[ControllerImpressoes::class, 'imprimirRelatorioFinal'])->name('imprimirRelatorioFinal');
Route::get('imprimir/ataVisitaTecnica/{id}',[ControllerImpressoes::class, 'imprimirAtaVisitaTecnica'])->name('imprimirAtaVisitaTecnica');
Route::get('imprimir/termoCompromisso/{id}',[ControllerImpressoes::class, 'imprimirTermoCompromisso'])->name('imprimirTermoCompromisso');
Route::get('imprimir/downloadTermoCompromisso/{id}/{discente}', [ControllerImpressoes::class, 'downloadTermoCompromisso'])->name('downloadTermoCompromisso');

