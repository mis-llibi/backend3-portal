<?php

namespace App\Http\Controllers\Dental_insurance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Self_service\Sync;
use App\Models\Dental_insurance\Members;
use App\Models\Dental_insurance\Addons;

class DentalInsuranceAuthController extends Controller
{
    public function login(Request $request)
    {
        $client = Sync::where('first_name', 'like', '%'.strtoupper($request->firstName).'%')
                    ->where('last_name', 'like', '%'.strtoupper($request->lastName).'%')
                    ->where('birth_date', $request->birthDate)
                    ->get();

        if(count($client) > 0) {

            $exist = Members::where('member_id', $client[0]->member_id)->exists();
            if(!$exist) {
                $principal = [
                    'member_id' => $client[0]->member_id,
                    'first_name' => $client[0]->first_name,
                    'last_name' => $client[0]->last_name,
                    'middle_name' => $client[0]->middle_name,
                    'birth_date' => $client[0]->birth_date,
                    'relation' => $client[0]->relation,
                    'status' => 1
                ];
                Members::create($principal);
            }

            session(['access' => 'logged in', 'member' => $client[0]]);
            $request->session()->regenerate();
            //return date('Y-m-d', strtotime($request->birth_date)); 
            return response()->noContent();

        } else {
            return response()->json([
                "message" => "These credentials do not match our records.",
                "errors" => [
                    'email' => ["These credentials do not match our records."]
                ],
            ], 422);
        }
    }

    public function checkSession(Request $request)
    {
        if(session('access') == 'logged in')
            return $request->session()->all()['member'];

            return response()->json([
                "message" => "Unauthenticated.",
            ], 401);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
