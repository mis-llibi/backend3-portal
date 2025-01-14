<?php

namespace App\Http\Controllers\Appcode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appcode\AppCode;

class AppCodeController extends Controller
{
    public function GetCode($id)
    {
        $client = AppCode::where('approval_code', 'like', '%'.$id.'%')
            ->whereNotNull('approval_code')
            ->orderBy('id', 'DESC')
            ->get([
                'approval_code',
                'loa_number',
                'employee_name',
                'patient_name',
                'datetime'
            ]);

        return $client;
    }
}
