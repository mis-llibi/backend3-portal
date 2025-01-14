<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\NotificationController;

class PreqinNotificationController extends Controller
{
  public function submittedWithDep($info)
  {
    $mailMsg = array(
      'subject' => 'PREQIN DEPENDENT ENROLLMENT NOTIFICATION',
      'body' => view('send-preqin-dependent-enrollment')
    );

    if (!empty($info['email'])) {
      (new NotificationController)->MailNotification($info['name'], $info['email'], $mailMsg);
    }
    if (!empty($info['email2'])) {
      (new NotificationController)->MailNotification($info['name'], $info['email2'], $mailMsg);
    }

    $smsBody =
      "From PREQIN & LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

    if (!empty($info['mobile_no'])) {
      (new NotificationController)->SmsNotification($info['mobile_no'], $smsBody);
    }
  }

  public function getDates()
  {
    return [
      'dateStart'        => '2024-01-19',
      'dateFinalWarning' => '2024-01-29',
      'dateFormLocked'   => '2024-01-30'
    ];
  }

  public function submittedWithoutDep($info)
  {
    $mailMsg = array(
      'subject' => 'PERQIN DEPENDENT ENROLLMENT NOTIFICATION',
      'body' => view('send-preqin-dependent-enrollment')
    );

    if (!empty($info['email'])) (new NotificationController)
      ->MailNotification($info['name'], $info['email'], $mailMsg);
    if (!empty($info['email2'])) (new NotificationController)
      ->MailNotification($info['name'], $info['email2'], $mailMsg);

    $smsBody =
      "From PERQIN & LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

    if (!empty($info['mobile_no'])) {
      (new NotificationController)->SmsNotification($info['mobile_no'], $smsBody);
    }
  }

  public function invite($info)
  {
    $mailMsg = array(
      'subject' => 'PREQIN DEPENDENT ENROLLMENT NOTIFICATION',
      'body' => view('send-preqin-invite', ['link' => 'http://localhost:3000/self-enrollment/preqin?id=' . $info['hash'] . ''])
    );

    if (!empty($info['email'])) {
      (new NotificationController)
        ->MailNotification($info['name'], $info['email'], $mailMsg);
    }

    if (!empty($info['email2'])) {
      (new NotificationController)->MailNotification($info['name'], $info['email2'], $mailMsg);
    }


    if (!empty($info['mobile'])) {
      $smsBody = "From PREQIN & LLIBI:\n\nGood day!\n\nWe are delighted to welcome PREQIN employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

      (new NotificationController)->SmsNotification($info['mobile'], $smsBody);
    }
  }

  public function approvedWithCertificateNo($info)
  {
    $insSms = implode("\n", $info['insSms']);
    $name = ucwords(strtolower($info['name']));

    $attachment = [
      'public/Self_enrollment/Broadpath/001Welcome_pack/BROADPATH - WELCOME LETTER.pdf',
      'public/Self_enrollment/Broadpath/001Welcome_pack/SOC_SME LUXE- BROADPATH.pdf',
    ];

    $mailMsg = array(
      'subject' => 'PREQIN DEPENDENT ENROLLMENT NOTIFICATION',
      'body' => view('send-approve-with-cert', [
        'deps' => $info['insMail'],
        'name' => $name
      ]),
      // 'attachment' => $attachment
    );

    if (!empty($info['email'])) {
      (new NotificationController)->MailNotification($info['name'], $info['email'], $mailMsg);
    }
    if (!empty($info['email2'])) {
      (new NotificationController)->MailNotification($info['name'], $info['email2'], $mailMsg);
    }

    if (!empty($info['mobile'])) {
      $smsBody =
        "From PREQIN & LLIBI:\n\nDear $name, \n\nGood day! \nWe are pleased to inform that your healthcare (HMO) plan membership under Philcare (and arranged through Lacson & Lacson Insurance Brokers, Inc.) is already active. \n\n$insSms \n\nYour physical membership card will be delivered to your home address within 3-4 weeks. In the meantime, you may present your virtual or digital card in the HeyPhil app or certificate number to Philcare’s accredited facilities, along with valid ID, in order to make claim a while awaiting for your physical card. \n\nThis is an auto-generated SMS. Doesn’t support replies and calls.";

      (new NotificationController)->SmsNotification($info['mobile'], $smsBody);
    }
  }
}
