<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $data;
    protected $chanel;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data, $chanel)
    {
        $this->data = $data;
        $this->chanel = $chanel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn($chanel)
    {
        return new PrivateChannel($chanel);
    }
    
    public function broadcastWith() {
        return [
            "foo" => "bar"
        ];
    }
}
