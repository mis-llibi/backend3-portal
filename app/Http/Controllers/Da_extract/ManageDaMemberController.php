<?php

namespace App\Http\Controllers\Da_extract;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Self_service\Sync;

class ManageDaMemberController extends Controller
{
    public function getMembers()
    {
        $members = Sync::get(['id', 'member_id as empNo', 'company_code as compCode', 'last_name as lastName', 'first_name as firstName', 'birth_date as birthDate', 'relation', 'email']);

        return $members;
    }
}
