<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Self_enrollment\ManageSelfEnrollmentController as SelfEnrollment;

//ADMIN
Route::post('/self-enrollment/import-clients/{company}', [SelfEnrollment::class, 'importClients'])
    ->middleware('auth');

Route::post('/self-enrollment/import-clients-for-cancellation/{company}', [SelfEnrollment::class, 'importClientCancellation'])
    ->middleware('auth');

Route::post('/self-enrollment/import-clients-for-approving/{company}', [SelfEnrollment::class, 'importClientForApproving'])
    ->middleware('auth');

Route::post('/self-enrollment/create-client/{company}', [SelfEnrollment::class, 'createClient'])
    ->middleware('auth');

Route::post('/self-enrollment/remove-client', [SelfEnrollment::class, 'removeClient'])
    ->middleware('auth');

Route::post('/self-enrollment/update-client/{company}', [SelfEnrollment::class, 'updateClient'])
    ->middleware('auth');

Route::post('/self-enrollment/export-clients/{company}', [SelfEnrollment::class, 'exportClients'])
    ->middleware('auth');

//RESENDING OF INVITE
Route::post('/self-enrollment/send-client-invite', [SelfEnrollment::class, 'sendClientInvite'])
    ->middleware('auth');

Route::post('/self-enrollment/resend-client-invite-for-milestone', [SelfEnrollment::class, 'sendClientInviteForMilestone'])
    ->middleware('auth');

//LIFE INSURANCE
Route::post('/self-enrollment/update-life-insurance', [SelfEnrollment::class, 'updateLifeInsurance'])
    ->middleware('auth');

//CLIENT
Route::post('/self-enrollment/update-client-info/{company}', [SelfEnrollment::class, 'updateClientInfo']);

Route::post('/self-enrollment/submit-dependent/{company}', [SelfEnrollment::class, 'submitDependent']);

Route::post('/self-enrollment/submit-without-dependent/{company}', [SelfEnrollment::class, 'submitWithoutDependent']);

//MILESTONE
Route::post('/self-enrollment/milestone-update/{company}', [SelfEnrollment::class, 'milestoneUpdate']);

//**IMAGE
Route::post('/self-enrollment/remove-uploaded-file', [SelfEnrollment::class, 'removeFile']);
