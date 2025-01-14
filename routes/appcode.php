<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Appcode\AppCodeController;

Route::get('/get-by-code/{id}', [AppCodeController::class, 'GetCode']);
