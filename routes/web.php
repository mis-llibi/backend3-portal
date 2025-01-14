<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return ['Laravel' => app()->version()];
});

Route::get('/test', function () {
  return view('send-complaint', ['link' => 'http://localhost:3000/self-enrollment/preqin?id=9213aab5d259d26abca0b5eff68025cd']);
});

require __DIR__ . '/auth.php';

require __DIR__ . '/self-service.php';

require __DIR__ . '/self-service-mobile.php';

require __DIR__ . '/dental-insurance.php';

require __DIR__ . '/appcode.php';

require __DIR__ . '/members.php';

require __DIR__ . '/self-enrollment.php';

require __DIR__ . '/company-policies.php';
