<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\Feedback\FeedbackCorporateController;
use App\Http\Controllers\Feedback\AdmuFeedbackController;

// feedback client portal
Route::post('/feedbacks', [FeedbackController::class, 'store'])->middleware('throttle:3,1');
Route::get('/feedbacks/{request_id}', [FeedbackController::class, 'checkingIfAlreadyFeedback']);
Route::get('/feedbacks/is-expired/{request_id}', [FeedbackController::class, 'checkingIfFeedbackLinkIsExpired']);
Route::get('/feedbacks', [FeedbackController::class, 'index']);
Route::get('/feedbacks-export', [FeedbackController::class, 'exportFeedbackReport']);
// feedback corporate
Route::get('/corporate/feedbacks', [FeedbackCorporateController::class, 'index']);
Route::post('/corporate/feedbacks', [FeedbackCorporateController::class, 'sendLoa']);
Route::post('/corporate/feedbacks/save', [FeedbackCorporateController::class, 'store']);
Route::get('/corporate/feedbacks/employee', [FeedbackCorporateController::class, 'showEmployee']);
Route::get('/corporate/feedbacks-export', [FeedbackCorporateController::class, 'exportFeedbackReport']);

// feedback admu
Route::get('/admu/feedbacks', [AdmuFeedbackController::class, 'index']);
Route::post('/admu/feedbacks', [AdmuFeedbackController::class, 'store'])->middleware('throttle:3,1');
Route::get('/admu/feedbacks-export', [AdmuFeedbackController::class, 'exportFeedbackReport']);
