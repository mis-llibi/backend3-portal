<?php

use App\Http\Controllers\Members\ManageMemberController;
use App\Http\Controllers\Members\ManageEnrolleeController;

//Enrollees
Route::post('/hr/import-enrollees', [ManageEnrolleeController::class, 'import'])
    ->middleware('auth');

Route::post('/hr/create-enrollee', [ManageEnrolleeController::class, 'create'])
    ->middleware('auth');

Route::post('/hr/update-enrollee', [ManageEnrolleeController::class, 'update'])
    ->middleware('auth');

Route::post('/hr/remove-enrollee', [ManageEnrolleeController::class, 'remove'])
    ->middleware('auth');

Route::post('/hr/for-enrollment-member', [ManageEnrolleeController::class, 'forEnrollment'])
    ->middleware('auth');

//Members
//***admin
Route::post('/hr/update-enrollment-status', [ManageEnrolleeController::class, 'updateEnrollmentStatus'])
    ->middleware('auth');

Route::post('/hr/approve-cancellation-member', [ManageEnrolleeController::class, 'approveCancellation'])
    ->middleware('auth');

//***client
Route::post('/hr/for-cancellation-member', [ManageEnrolleeController::class, 'forCancellation'])
    ->middleware('auth');

Route::post('/hr/revoke-cancellation-member', [ManageEnrolleeController::class, 'revokeCancellation'])
    ->middleware('auth');

Route::post('/hr/for-member-correction', [ManageEnrolleeController::class, 'forCorrection'])
    ->middleware('auth');

Route::post('/hr/revoke-correction-member', [ManageEnrolleeController::class, 'revokeCorrection'])
    ->middleware('auth');