<?php

namespace App\Events\ClientPortal;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberNotificationEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public array $data;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct(array $data)
  {
    $this->data = $data;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('channel-name');
  }
}
