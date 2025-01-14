<?php

use App\Http\Controllers\Self_service\ClientController;
use Illuminate\Support\Facades\Route;

Route::post('/self-service/validate-client', [ClientController::class, 'ValidateClient']);
Route::post('/self-service/track-reference-number', [ClientController::class, 'TrackReferenceNumber']);

Route::get('/self-service/get-request/{refno}', [ClientController::class, 'GetRequest']);
Route::post('/self-service/update-request', [ClientController::class, 'UpdateRequest']);

Route::get('/self-service/client-search-hospital', [ClientController::class, 'SearchHospital']);
Route::get('/self-service/client-search-doctor', [ClientController::class, 'SearchDoctor']);

use App\Http\Controllers\Self_service\ProviderController;

Route::get('/self-service/provider-search-request/{search}/{id}', [ProviderController::class, 'SearchRequest']);
Route::post('/self-service/provider-update-request', [ProviderController::class, 'UpdateRequest']);

use App\Http\Controllers\Self_service\AdminController;

Route::get('/self-service/admin-search-request/{search}/{id}', [AdminController::class, 'SearchRequest']);
Route::post('/self-service/admin-update-request', [AdminController::class, 'UpdateRequest']);
Route::get('/self-service/get-companies', [AdminController::class, 'GetCompanies']);
