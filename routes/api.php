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

Route::apiResource('omset', ReportController::class);

Route::get('/omset-global', [ReportController::class, 'omsetGlobalSales'])->name('omset.globalSales');

//Cetak Struk Jasa
Route::get('/cetak-struk/{id}', [StrukController::class, 'notaOrderJson'])->name('cetakStruk');

//Cetak Struk Retail
Route::get('/daftar-retail/{id}', [ReportController::class, 'retailJson'])->name('retailJson');
Route::get('/sales', [ReportController::class, 'listSales'])->name('sales');
Route::get('/retail-cetak/{id}', [ReportController::class, 'retailCetakById'])->name('retailCetakById');
Route::get('/sales-info/{id}', [ReportController::class, 'salesInfo'])->name('salesInfo');