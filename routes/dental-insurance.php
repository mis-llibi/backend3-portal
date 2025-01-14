<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Dental_insurance\DentalInsuranceAuthController;

Route::post('/dental-insurance/validate-member', [DentalInsuranceAuthController::class, 'login']);
Route::post('/dental-insurance/logout', [DentalInsuranceAuthController::class, 'logout']);

use App\Http\Controllers\Dental_insurance\DentalInsuranceManageMemberController;

Route::post('/dental-insurance/create', [DentalInsuranceManageMemberController::class, 'create']);
Route::post('/dental-insurance/update', [DentalInsuranceManageMemberController::class, 'update']);
Route::post('/dental-insurance/delete', [DentalInsuranceManageMemberController::class, 'delete']);