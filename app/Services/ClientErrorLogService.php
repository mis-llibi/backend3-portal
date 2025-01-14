<?php

namespace App\Services;

use App\Http\Controllers\NotificationController;
use App\Models\Self_service\ClientErrorLog;
use Illuminate\Support\Facades\Log;
use App\Services\GetActiveEmailProvider;

use Illuminate\Support\Str;

class ClientErrorLogService
{

  public static function saveLog($inputs)
  {
    $errorLog = ClientErrorLog::create([
      'request_type' => $inputs['requestType'],

      'member_id' => $inputs['memberID'],
      'first_name' => $inputs['firstName'],
      'last_name' => $inputs['lastName'],
      'dob' => $inputs['dob'],

      'is_dependent' => $inputs['minorDependent'],
      'dependent_member_id' => $inputs['depMemberID'],
      'dependent_first_name' => $inputs['depFirstName'],
      'dependent_last_name' => $inputs['depLastName'],
      'dependent_dob' => $inputs['depDob'],
      'request_loa_type' => Str::upper($inputs['typeLOA']),
    ]);

    // Log::info($errorLog);
    if (env('NODE_ENV') == 'production') {
      $mailMsg = view('send-error-logs', ['data' => $errorLog]);
      switch (GetActiveEmailProvider::getProvider()) {
        case 'infobip':
          $emailer = new SendingEmail(email: env('GLEN'), body: $mailMsg, subject: 'CLIENT CARE PORTAL ERROR LOGS - NOTIFICATION');
          $emailer->send();
          break;

        default:
          // $body = array('body' => $mailMsg, 'attachment' => [], 'cc' => [env('SIR_SEB')]);
          // $mail = (new NotificationController)->NewMail('', env('GLEN'), $body);

          break;
      }
    }

    return $errorLog;
  }
}
