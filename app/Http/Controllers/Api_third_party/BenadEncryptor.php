<?php

namespace App\Http\Controllers\Api_third_party;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Corporate\CompaniesSync;
use App\Services\SendingEmail;
use Illuminate\Support\Facades\Log;

class BenadEncryptor extends Controller
{
  public function GetCompanies()
  {
    $request = CompaniesSync::get(['id', 'corporate_compcode', 'name', 'benad_password']);

    return (count($request) > 0 ? $request : '');
  }

  public function UpdateCompanyPassword(Request $request)
  {
    $update = [
      'benad_password' => $request->password,
    ];

    $update = CompaniesSync::where('id', $request->id)
      ->update($update);
  }

  public function sendEmailPassword(Request $request)
  {
    $email = $request->email;
    $password = $request->password;
    $cc = $request->filled('cc') ? explode(',', $request->cc) : null;

    $array_cc = [];
    if ($request->filled('cc')) {
      foreach ($cc as $key => $item) {
        array_push($array_cc, $item);
      }
    }

    $body = view('ebd.encrypt-files', compact('password'));

    $sending = new SendingEmail($email, $body, 'LLIBI PASSWORD', cc: $array_cc);
    $sending->sendLlibiBenAd();

    return response()->noContent();
  }
}
