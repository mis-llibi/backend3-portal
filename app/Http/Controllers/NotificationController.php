<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\SendingEmail;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
  // -- All Notification goes here via SMS and EMAIL
  public function SmsNotification($mobile, $message)
  {
    $ch = curl_init('http://192.159.66.221/goip/sendsms/');

    $parameters = array(
      'auth' => array('username' => "root", 'password' => "LACSONSMS"), //Your API KEY
      'provider' => "SIMNETWORK2",
      'number' => $mobile,
      'content' => $message,
    );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //Send the parameters set above with the request
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

    // Receive response from server
    $output = curl_exec($ch);
    curl_close($ch);

    //print_r(json_decode($output));
    //Show the server response
  }

  public function OldMailNotification($name, $email, $message)
  {
    Mail::send([], [], function ($mail) use ($name, $email, $message) {

      $subject = (isset($message['subject']) ? $message['subject'] : 'Client Care Portal - Notification');

      $mail->to($email, $name)
        ->subject($subject)
        ->html('<h4>' . $message['body'] . '</h4>');
      $mail->from('notify@llibi.app');

      if (isset($message['bcc'])) {
        $mail->bcc($message['bcc'], $message['name']);
      }

      if (isset($message['attachment'])) {
        foreach ($message['attachment'] as $file) {
          $mail->attach(Storage::path($file));
        }
      }
    });
  }

  public function MailNotification($name, $email, $message)
  {
    // Mail::send([], [], function ($mail) use ($name, $email, $message) {

    //   $subject = (isset($message['subject']) ? $message['subject'] : 'Client Care Portal - Notification');

    //   $mail->to($email, $name)
    //     ->subject($subject)
    //     ->html('<h4>' . $message['body'] . '</h4>');
    //   $mail->from('notify@llibi.app');

    //   if (isset($message['bcc'])) {
    //     $mail->bcc($message['bcc'], $message['name']);
    //   }

    //   if (isset($message['attachment'])) {
    //     foreach ($message['attachment'] as $file) {
    //       $mail->attach(Storage::path($file));
    //     }
    //   }
    // });

    $body = $message['body'];
    $subject = isset($message['subject']) ? $message['subject'] : 'CLIENT CARE PORTAL - NOTIFICATION';
    $cc = [];
    $bcc = [];
    $attachment = [];

    if (isset($message['cc'])) {
      if (is_array($message['cc'])) {
        foreach ($message['cc'] as $key => $row) {
          array_push($cc, $row);
        }
      } else {
        array_push($cc, $message['cc']);
      }
    }

    if (isset($message['bcc'])) {
      if (is_array($message['bcc'])) {
        foreach ($message['bcc'] as $key => $row) {
          array_push($bcc, $row);
        }
      } else {
        array_push($bcc, $message['bcc']);
      }
    }

    if (isset($message['attachment'])) {
      foreach ($message['attachment'] as $file) {
        array_push($attachment, Storage::path($file));
      }
    }

    try {
      $emailer = new SendingEmail(
        email: $email,
        body: $body,
        subject: $subject,
        attachments: $attachment,
        cc: $cc,
        bcc: $bcc
      );
      $emailer->send();
    } catch (\Throwable $th) {
      //throw $th;
      Log::error($th);
    }
  }

  public function EncryptedPDFMailNotification($name, $email, $message)
  {
    // try {
    //   //Mail::to($userEmail)->send($welcomeMailable);
    //   Mail::send([], [], function ($mail) use ($name, $email, $message) {

    //     $subject = (isset($message['subject']) ? $message['subject'] : 'Client Care Portal - Notification');

    //     $mail->to($email, $name)
    //       ->subject($subject)
    //       ->html('<h4>' . $message['body'] . '</h4>');
    //     $mail->from('notify@llibi.app');

    //     if (isset($message['bcc']))
    //       $mail->bcc($message['bcc'], $message['name']);

    //     if (isset($message['attachment']))
    //       foreach ($message['attachment'] as $file) {
    //         $mail->attach($file);
    //       }
    //   });
    // } catch (Exception $e) {
    //   print_r($e);
    // }

    $body = $message['body'];
    $subject = isset($message['subject']) ? $message['subject'] : 'CLIENT CARE PORTAL - NOTIFICATION';
    $cc = [];
    $bcc = [];
    $attachment = [];

    if (isset($message['cc'])) {
      if (is_array($message['cc'])) {
        foreach ($message['cc'] as $key => $row) {
          array_push($cc, $row);
        }
      } else {
        array_push($cc, $message['cc']);
      }
    }

    if (isset($message['bcc'])) {
      if (is_array($message['bcc'])) {
        foreach ($message['bcc'] as $key => $row) {
          array_push($bcc, $row);
        }
      } else {
        array_push($bcc, $message['bcc']);
      }
    }

    if (isset($message['attachment'])) {
      foreach ($message['attachment'] as $file) {
        array_push($attachment, $file);
      }
    }

    try {
      $emailer = new SendingEmail(
        email: $email,
        body: $body,
        subject: $subject,
        attachments: $attachment,
        cc: $cc,
        bcc: $bcc
      );
      $emailer->send();
    } catch (\Throwable $th) {
      //throw $th;
      Log::error($th);
    }
  }

  public function NewMail($name, $email, $message)
  {
    Mail::send([], [], function ($mail) use ($name, $email, $message) {

      $subject = (isset($message['subject']) ? $message['subject'] : 'CLIENT CARE PORTAL - NOTIFICATION');

      $mail->to($email)
        ->subject($subject)
        ->html($message['body']);
      // $mail->from('notify@llibi.app', 'Lacson & Lacson Insurance Brokers Inc.');

      if (isset($message['bcc'])) {
        $mail->bcc($message['bcc']);
      }

      if (isset($message['cc'])) {
        $mail->cc($message['cc']);
      }

      if (isset($message['attachment'])) {
        foreach ($message['attachment'] as $file) {
          $mail->attach(Storage::path($file));
        }
      }
    });
  }
}
