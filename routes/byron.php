<?php

use App\Http\Controllers\HealthdashController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

Route::get('/healthdash/emails', [HealthdashController::class, 'index']);
