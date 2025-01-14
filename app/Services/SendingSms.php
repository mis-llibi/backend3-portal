<?php

namespace App\Services;

use GuzzleHttp\Client;

class SendingSms
{
  public function __construct(public $number, public $message)
  {
  }

  private function sendSms()
  {
    try {
      $post_data = [
        'multipart' => [
          [
            'name' => 'api_key',
            'contents' => config('app.movider_key')
          ],
          [
            'name' => 'api_secret',
            'contents' => config('app.movider_secret')
          ],
          [
            'name' => 'from',
            'contents' => 'MOVIDER'
          ],
          [
            'name' => 'to',
            'contents' => $this->number
          ],
          [
            'name' => 'text',
            'contents' => $this->message
          ],
        ]
      ];
      $client = new Client();
      $response = $client->post('https://api.movider.co/v1/sms', $post_data);
      $body = $response->getBody();
      return true;
      // return json_decode($body);
    } catch (\Throwable $th) {
      echo $th;
      return;
    }
  }

  public function send()
  {
    $this->sendSms();
  }
}
