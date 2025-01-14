<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api_third_party\BenadEncryptor;

Route::get('/ebdencrypt/show-companies', [BenadEncryptor::class, 'GetCompanies']);
Route::post('/ebdencrypt/update-password', [BenadEncryptor::class, 'UpdateCompanyPassword']);
Route::post('/ebdencrypt/send-email-password', [BenadEncryptor::class, 'sendEmailPassword']);
