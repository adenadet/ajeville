<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }
    
    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.'.$this->chatMessage->room_id)];
    }

    public function broadcastAs(){
        return 'message.sent';
    }

    public function broadcastWith(){
        return [
            'room_id' => $this->chatMessage->room_id,
            'message' => $this->chatMessage->toArray(),
        ];
    }
}
