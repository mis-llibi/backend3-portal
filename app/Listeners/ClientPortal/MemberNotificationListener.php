<?php

namespace App\Listeners\ClientPortal;

use App\Models\ClientPortalErrorLogs;
use App\Services\SendingEmail;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberNotificationListener
{
  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   *
   * @param  object  $event
   * @return void
   */
  public function handle($event)
  {
    $data = $event->data;
    Log::debug($data);

    $notify_to = $data['notify_to'];
    $email = $data['member_info']['email'];
    $member_info = $data['member_info'];

    $status = 0;
    try {
      DB::beginTransaction();
      switch ($notify_to) {
        case 'member':
          $body = view('client-error-logs.send-notify-member');
          $sending = new SendingEmail($email, $body);
          $sending->send();
          $status = 1;
          break;
        case 'mis':
          $remarks = $data['remarks'];
          $body = view('client-error-logs.send-notify-mis', [
            'member_info' => $member_info,
            'remarks' => $remarks,
          ]);

          $sending = new SendingEmail(env('EDP_EMAIL', 'glenilagan@llibi.com'), $body);
          $sending->send();
          $status = 2;
          break;
        case 'cae':
          $cae_email = $data['cae_email'];
          $remarks = $data['remarks'];

          // send to member
          $member_body = view('client-error-logs.send-notify-member-2-3-days');
          $sending = new SendingEmail($email, $member_body);
          $sending->send();

          // send to cae
          $cae_body = view('client-error-logs.send-notify-cae', [
            'member_info' => $member_info,
            'remarks' => $remarks,
          ]);
          $sending = new SendingEmail($cae_email, $cae_body);
          $sending->send();
          $status = 3;
          break;
        default:
          throw new Exception("Request not supported");
          break;
      }

      ClientPortalErrorLogs::where('id', $member_info['id'])
        ->update([
          'notify_status' => $status,
        ]);

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      throw Log::error($th->getMessage());
    }
  }
}
