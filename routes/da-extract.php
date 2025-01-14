<?php

use App\Http\Controllers\Members\ManageMemberController;

//Members
Route::post('/import-member', [ManageMemberController::class, 'import'])
    ->middleware('auth');

Route::post('/create-member', [ManageMemberController::class, 'create'])
    ->middleware('auth');

Route::post('/update-member', [ManageMemberController::class, 'update'])
    ->middleware('auth');