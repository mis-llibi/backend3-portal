<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Self_service\MobileLogin;

class MobileAuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function store(LoginRequest $request)
    {
        $client = MobileLogin::where('member_id', strtoupper($request->member_id))
                      ->where('birth_date', $request->birth_date)
                      ->get();

        if(count($client) > 0) {
            session(['access' => 'logged in', 'user' => $client[0]]);
            $request->session()->regenerate();
            return date('Y-m-d', strtotime($request->birth_date));
            //response()->noContent();
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
            return $request->session()->all();

            return response()->json([
                "message" => "Unauthenticated.",
            ], 401);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
