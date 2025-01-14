<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SelfServiceMobile\ClientController;

Route::post('/validate-client-mobile', [ClientController::class, 'ValidateClient']);

Route::get('/get-request-mobile/{refno}', [ClientController::class, 'GetRequest']);
Route::post('/update-request-mobile', [ClientController::class, 'UpdateRequest']);

Route::get('/client-search-hospital-mobile', [ClientController::class, 'SearchHospital']);
Route::get('/client-search-doctor-mobile', [ClientController::class, 'SearchDoctor']);