<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;

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

Route::get('/pos/nota/{id}', [PosController::class, 'notaPenjualan'])->name('pos.notaPenjualan');

Route::get('/omset', [ReportController::class, 'omsetGlobalSales'])->name('omset.globalSales');
