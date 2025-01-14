<?php

namespace App\Http\Controllers;

use App\Models\ReportSetting;
use Illuminate\Http\Request;

use App\Services\SendingEmail;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
  public function index()
  {
    return ReportSetting::query()->first();
  }

  public function update(Request $request)
  {
    $settings = ReportSetting::find(1);

    $settings->minutes = $request->minutes;
    $settings->receiver = $this->clean($request->receiver);
    $settings->receiver_email = $request->receiver_email;
    $settings->save();

    return response()->json(['status' => true, 'message' => 'Update success.']);
  }

  function clean($string)
  {
    $string = str_replace('-', '', $string);
    $string = str_replace('+63', '0', $string);

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
  }
}
