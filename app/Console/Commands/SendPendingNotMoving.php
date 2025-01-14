<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationController;
use Illuminate\Console\Command;

use App\Http\Controllers\Self_service\AutoSendPendingNotMoving;
use App\Mail\EmailPendingNotMoving;
use App\Models\Self_service\Client;
use App\Models\ReportSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\SendingEmail;
use Carbon\Carbon;

class SendPendingNotMoving extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'send:pending-not-moving';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Auto send all pending with elapse 15 minutes';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    set_time_limit(0);
    $controller = new AutoSendPendingNotMoving();
    $result = $controller->autoSendEmail();
    $setting = ReportSetting::find(1);

    $email_to = $setting->receiver_email;
    $email_to_array = explode(",", $email_to);

    foreach ($result as $key => $row) {
      $refno = $row->refno;
      $email = $row->email;
      $contact = $row->contact;
      $memberID = $row->memberID;
      $firstName = $row->firstName;
      $lastName = $row->lastName;
      $company_name = $row->company_name;
      $createdAt = $row->createdAt;

      $elapse_minutes = Carbon::now()->diffInMinutes($createdAt, false);

      $body = view('send-pending-not-moving', [
        'refno' => $refno,
        'email' => $email,
        'contact' => $contact,
        'memberID' => $memberID,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'company_name' => $company_name,
        'minutes' => $setting->minutes,
      ]);
      $subject = 'CLIENT CARE PORTAL - ALERT';

      if ($elapse_minutes > $setting->minutes) {
        foreach ($email_to_array as $key => $new_email_to) {
          switch (env('EMAIL_PROVIDER_SETTING', 'default')) {
            case 'infobip':
              $emailer = new SendingEmail($new_email_to, $body, $subject);
              $response = $emailer->send();
              break;

            default:
              (new NotificationController)->NewMail('', $setting->receiver, ['body' => $body, 'subject' => $subject]);
              break;
          }
        }

        $sms_message = "Lacson & Lacson Alert:\n\nRequest for $lastName, $firstName has not been attended for more than $setting->minutes minutes.";
        (new NotificationController)->SmsNotification($setting->receiver, $sms_message);

        if ($response) {
          Client::where('id', $row->id)->update(['is_sent' => 1]);
        }
      }
    }
  }
}
