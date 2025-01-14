<?php

namespace App\Http\Controllers\Company_policies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company_policies\Company_policies;

class PoliciesController extends Controller
{
    //
    public function getPolicies()
    {
        $policies = Company_policies::all();
        return response()->json($policies);
    }

    //upload policies use llibiapp
    public function uploadPolicies(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
        ]);
        $company_id = $request->company_id;

        // Insert data in company_policies
        


        $file = $request->file('file');
        $path = $file->store('Policies', 'llibiapp');
        $url = config('filesystems.disks.llibiapp.cdn_endpoint')."/".$path;
        return response()->json(['url' => $url]);
    }

}
