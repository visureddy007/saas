<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorChannelBroadcast implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $message;

    public $vendorUid;

    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(string $vendorUid, array $data)
    {
        //
        $this->message = 'vendor-broadcast';
        $this->vendorUid = $vendorUid;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendor-channel.'.$this->vendorUid),
        ];
    }

    public function broadcastAs()
    {
        return 'VendorChannelBroadcast';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        if (! empty($this->data)) {
            return $this->data;
        }

        return [];
    }
}
