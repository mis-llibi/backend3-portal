<?php

use App\Http\Controllers\Company_policies\PoliciesController;

Route::get('/company-policies/get-policies', [PoliciesController::class, 'getPolicies']);