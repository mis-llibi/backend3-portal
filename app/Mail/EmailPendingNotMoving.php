<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailPendingNotMoving extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(
    public $refno,
    public $email,
    public $contact,
    public $memberID,
    public $firstName,
    public $lastName,
    public $company_name
  ) {
    //
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject('CLIENT PORTAL ALERT')->view('send-pending-not-moving');
  }
}
