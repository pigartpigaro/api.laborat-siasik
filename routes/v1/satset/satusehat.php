<?php

// use App\Http\Controllers\Api\Satusehat\OrganizationController;

use App\Http\Controllers\Api\Satusehat\AuthController;
use App\Http\Controllers\Api\Satusehat\KunjunganSatsetController;
use App\Http\Controllers\Api\Satusehat\LocationController;
use App\Http\Controllers\Api\Satusehat\OrganizationController;
use App\Http\Controllers\Api\Satusehat\PractitionerController;
use Illuminate\Support\Facades\Route;



Route::group([
    'middleware' => 'auth:api',
    // 'middleware' => 'jwt.verify',
    'prefix' => 'satusehat'
], function () {
    // AUTH
    Route::get('/authorization', [AuthController::class, 'index']);

    // ORGANISATION
    Route::get('/listOrganisasiRs', [OrganizationController::class, 'listOrganisasiRs']);
    Route::post('/postOrganisasiRs', [OrganizationController::class, 'postOrganisasiRs']);
    Route::get('/organization', [OrganizationController::class, 'cariorganisasidisatset']);
    Route::get('/sendToSatset', [OrganizationController::class, 'sendToSatset']);

    // LOCATION
    Route::get('/listRuanganRajal', [LocationController::class, 'listRuanganRajal']);
    Route::post('/updateLocation', [LocationController::class, 'updateLocation']);

    // PRACTITIONER / NAKES
    Route::get('/listPractitioner', [PractitionerController::class, 'listPractitioner']);
    Route::post('/getPractitionerSatset', [PractitionerController::class, 'getPractitionerSatset']);

    // Kunjungan
    Route::get('/listKunjungan', [KunjunganSatsetController::class, 'listKunjungan']);
    Route::post('/getPasienByNikSatset', [KunjunganSatsetController::class, 'getPasienByNikSatset']);
    Route::post('/kirimKunjungan', [KunjunganSatsetController::class, 'kirimKunjungan']);
});
