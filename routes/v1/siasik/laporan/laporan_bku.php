<?php

use App\Http\Controllers\Api\Siasik\Laporan\BKUController;
use Illuminate\Support\Facades\Route;


Route::group([
    // 'middleware' => 'auth:api',
    'prefix' => 'laporan/laporan_bku'
], function () {

    Route::get('/bkuppk', [BKUController::class, 'bkuppk']);
    Route::get('/bkubpl', [BKUController::class, 'bkubpl']);

    Route::get('/spm', [BKUController::class,'spm']);
    Route::get('/panjar', [BKUController::class,'panjar']);
});


