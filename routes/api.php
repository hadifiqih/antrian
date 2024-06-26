<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Api\StrukController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/cetak-struk/{id}', [StrukController::class, 'notaOrderJson'])->name('cetakStruk');

Route::apiResource('omset', ReportController::class);

Route::get('/omset-global', [ReportController::class, 'omsetGlobalSales'])->name('omset.globalSales');


