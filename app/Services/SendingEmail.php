<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Mail\Attachment as MailAttachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use MailerSend\Helpers\Builder\Attachment;
// MAILERSEND PROVIDER
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\MailerSend;

class SendingEmail
{
  public function __construct(
    public $email = null,
    public $body = null,
    public $subject = 'CLIENT CARE PORTAL - NOTIFICATION',
    public $key = 'default',
    public $attachments = [],
    public $cc = [],
    public $bcc = []
  ) {
  }

  private function defaultSendMail()
  {
    try {
      $post_data = [
        'headers' => ['Authorization' => env('INFOBIP_API_KEY')],
        'multipart' => [
          [
            'name' => 'bulkId',
            'contents' => trim(Str::slug($this->subject))
          ],
          [
            'name' => 'from',
            'contents' => env('INFOBIP_SENDER'),
          ],
          [
            'name' => 'to',
            'contents' => trim($this->email)
          ],
          [
            'name' => 'subject',
            'contents' => trim($this->subject)
          ],
          [
            'name' => 'html',
            'contents' => $this->body
          ],
          [
            'name' => 'intermediateReport',
            'contents' => 'true'
          ],
          [
            'name' => 'track',
            'contents' => false
          ],
          [
            'name' => 'trackClicks',
            'contents' => false
          ],
          [
            'name' => 'trackOpens',
            'contents' => false
          ],
          [
            'name' => 'trackingUrl',
            'contents' => false
          ],
        ]
      ];

      if (!empty($this->attachments)) {
        foreach ($this->attachments as $key => $file) {
          array_push($post_data['multipart'], [
            'name' => 'attachment',
            'contents' => fopen($file, 'r')
          ]);
        }
      }
      if (!empty($this->cc)) {
        foreach ($this->cc as $key => $cc) {
          array_push($post_data['multipart'], [
            'name' => 'cc',
            'contents' => $cc
          ]);
        }
      }
      if (!empty($this->bcc)) {
        foreach ($this->bcc as $key => $bcc) {
          array_push($post_data['multipart'], [
            'name' => 'bcc',
            'contents' => $bcc
          ]);
        }
      }
      $client = new Client();
      $response = $client->post(env('INFOBIP_API_URL') . '/email/3/send', $post_data);
      $body = $response->getBody();

      Log::info('EMAIL SENT');
      return true;
      // return json_decode($body);
    } catch (\Throwable $th) {
      echo $th;
      Log::error($th);
      return false;
    }
  }

  private function llibiSendMail()
  {
    try {
      $post_data = [
        'headers' => ['Authorization' => env('INFOBIP_API_KEY')],
        'multipart' => [
          [
            'name' => 'bulkId',
            'contents' => trim(Str::slug($this->subject))
          ],
          [
            'name' => 'from',
            'contents' => env('INFOBIP_SENDER_NOTIFY'),
          ],
          [
            'name' => 'to',
            'contents' => trim($this->email)
          ],
          [
            'name' => 'subject',
            'contents' => trim($this->subject)
          ],
          [
            'name' => 'html',
            'contents' => $this->body
          ],
          [
            'name' => 'intermediateReport',
            'contents' => 'true'
          ],
          [
            'name' => 'track',
            'contents' => false
          ],
          [
            'name' => 'trackClicks',
            'contents' => false
          ],
          [
            'name' => 'trackOpens',
            'contents' => false
          ],
          [
            'name' => 'trackingUrl',
            'contents' => false
          ],
        ]
      ];

      if (!empty($this->attachments)) {
        foreach ($this->attachments as $key => $file) {
          array_push($post_data['multipart'], [
            'name' => 'attachment',
            'contents' => fopen($file, 'r')
          ]);
        }
      }
      if (!empty($this->cc)) {
        foreach ($this->cc as $key => $cc) {
          array_push($post_data['multipart'], [
            'name' => 'cc',
            'contents' => $cc
          ]);
        }
      }
      if (!empty($this->bcc)) {
        foreach ($this->bcc as $key => $bcc) {
          array_push($post_data['multipart'], [
            'name' => 'bcc',
            'contents' => $bcc
          ]);
        }
      }
      $client = new Client();
      $response = $client->post(env('INFOBIP_API_URL') . '/email/3/send', $post_data);
      $body = $response->getBody();

      Log::info('EMAIL SENT');
      return true;
      // return json_decode($body);
    } catch (\Throwable $th) {
      echo $th;
      Log::error($th);
      return false;
    }
  }
  
  private function llibiBenAdSendMail()
  {
    try {
      $post_data = [
        'headers' => ['Authorization' => env('INFOBIP_API_KEY')],
        'multipart' => [
          [
            'name' => 'bulkId',
            'contents' => trim(Str::slug($this->subject))
          ],
          [
            'name' => 'from',
            'contents' => env('INFOBIP_SENDER_NOTIFY_JOYCE'),
          ],
          [
            'name' => 'to',
            'contents' => trim($this->email)
          ],
          [
            'name' => 'subject',
            'contents' => trim($this->subject)
          ],
          [
            'name' => 'html',
            'contents' => $this->body
          ],
          [
            'name' => 'intermediateReport',
            'contents' => 'true'
          ],
          [
            'name' => 'track',
            'contents' => false
          ],
          [
            'name' => 'trackClicks',
            'contents' => false
          ],
          [
            'name' => 'trackOpens',
            'contents' => false
          ],
          [
            'name' => 'trackingUrl',
            'contents' => false
          ],
        ]
      ];

      if (!empty($this->attachments)) {
        foreach ($this->attachments as $key => $file) {
          array_push($post_data['multipart'], [
            'name' => 'attachment',
            'contents' => fopen($file, 'r')
          ]);
        }
      }
      if (!empty($this->cc)) {
        foreach ($this->cc as $key => $cc) {
          array_push($post_data['multipart'], [
            'name' => 'cc',
            'contents' => $cc
          ]);
        }
      }
      if (!empty($this->bcc)) {
        foreach ($this->bcc as $key => $bcc) {
          array_push($post_data['multipart'], [
            'name' => 'bcc',
            'contents' => $bcc
          ]);
        }
      }
      $client = new Client();
      $response = $client->post(env('INFOBIP_API_URL') . '/email/3/send', $post_data);
      $body = $response->getBody();

      Log::info('EMAIL SENT');
      return true;
      // return json_decode($body);
    } catch (\Throwable $th) {
      echo $th;
      Log::error($th);
      return false;
    }
  }

  private function testSendMail($sender)
  {
    try {
      $post_data = [
        'headers' => ['Authorization' => config('app.infobip_key')],
        'multipart' => [
          [
            'name' => 'bulkId',
            'contents' => trim(Str::slug($this->subject))
          ],
          [
            'name' => 'from',
            'contents' => $sender,
          ],
          [
            'name' => 'to',
            'contents' => trim($this->email)
          ],
          [
            'name' => 'subject',
            'contents' => trim($this->subject)
          ],
          [
            'name' => 'html',
            'contents' => $this->body
          ],
          [
            'name' => 'intermediateReport',
            'contents' => true
          ],
          [
            'name' => 'track',
            'contents' => false
          ],
          [
            'name' => 'trackClicks',
            'contents' => false
          ],
          [
            'name' => 'trackOpens',
            'contents' => false
          ],
          [
            'name' => 'trackingUrl',
            'contents' => false
          ],
        ]
      ];

      if (!empty($this->attachments)) {
        foreach ($this->attachments as $key => $file) {
          array_push($post_data['multipart'], [
            'name' => 'attachment',
            'contents' => fopen($file, 'r')
          ]);
        }
      }
      if (!empty($this->cc)) {
        foreach ($this->cc as $key => $cc) {
          array_push($post_data['multipart'], [
            'name' => 'cc',
            'contents' => $cc
          ]);
        }
      }
      if (!empty($this->bcc)) {
        foreach ($this->bcc as $key => $bcc) {
          array_push($post_data['multipart'], [
            'name' => 'bcc',
            'contents' => $bcc
          ]);
        }
      }
      $client = new Client();
      $response = $client->post(config('app.infobip_url') . '/email/3/send', $post_data);
      $body = $response->getBody();

      Log::info('EMAIL SENT');
      return true;
      // return json_decode($body);
    } catch (\Throwable $th) {
      echo $th;
      Log::error($th);
      return false;
    }
  }

  private function mailerSendProvider(string $body, array $email, string $subject, array $cc, array $bcc, array $attachments)
  {
    try {
      //code...
      $mailersend = new MailerSend(['api_key' => env('MAILERSEND_API_KEY')]);

      $subject = !empty($subject) ? Str::upper($subject) : 'CLIENT CARE PORTAL - NOTIFICATION';
      $cc = !empty($cc) ? $cc : [];
      $bcc = !empty($bcc) ? $bcc : [];
      $attachments = !empty($attachments) ? $attachments : [];

      $emailParams = (new EmailParams())
        ->setFrom(env('MAILERSEND_API_ADDRESS_FROM'))
        ->setFromName(env('MAILERSEND_API_ADDRESS_FROMNAME'))
        ->setSubject($subject)
        ->setHtml($body);

      if (!empty($email)) {
        foreach ($email as $key => $item) {
          $emailParams->setRecipients([new Recipient($item, '')]);
        }
      }

      if (!empty($cc)) {
        foreach ($cc as $key => $item) {
          $emailParams->setCc([new Recipient($item, '')]);
        }
      }

      if (!empty($bcc)) {
        foreach ($bcc as $key => $item) {
          $emailParams->setBcc([new Recipient($item, '')]);
        }
      }

      if (!empty($attachments)) {
        // $emailParams->setAttachments([]);
      }

      $mailersend->email->send($emailParams);

      Log::info('EMAIL SENT');
      return true;
    } catch (\Throwable $th) {
      Log::error($th);
      throw $th;
      return false;
    }
  }

  public function send()
  {
    return $this->defaultSendMail();
  }

  public function sendLlibi()
  {
    return $this->llibiSendMail();
  }

  public function sendLlibiBenAd()
  {
    return $this->llibiBenAdSendMail();
  }

  public function testSend($sender)
  {
    return $this->testSendMail($sender);
  }

  public function sendMailerSendProvider(string $body, array $email, string $subject, array $cc, array $bcc, array $attachments)
  {
    return $this->mailerSendProvider($body, $email, $subject, $cc, $bcc, $attachments);
  }
}
