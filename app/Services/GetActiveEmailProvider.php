<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class GetActiveEmailProvider
{
  public static function getProvider()
  {
    return DB::table('provider_settings')->where('id', 1)->value('provider');
  }
}
