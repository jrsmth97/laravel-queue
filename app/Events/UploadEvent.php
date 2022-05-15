<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $status;
    public $uploadId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($uploadId, $status)
    {
        $this->uploadId = $uploadId;
        $this->status = $status;
    }

     /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('UploadChannel');
    }

    /*
     * The Event's broadcast name.
     * 
     * @return string
     */
    public function broadcastAs()
    {
        return 'UploadEvent';
    }
    
     /*
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'upload_id' => $this->uploadId,
        	'status' => $this->status
        ];
    }
}
