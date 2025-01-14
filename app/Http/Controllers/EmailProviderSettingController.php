<?php

namespace App\Http\Controllers;

use App\Services\GetActiveEmailProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailProviderSettingController extends Controller
{
  public function __invoke(Request $request)
  {
    abort_if(!$request->provider, 422, 'No selected provider.');

    $provider = DB::table('provider_settings')->where('id', 1)->update(['provider' => $request->provider]);

    return response($provider);
  }
}
