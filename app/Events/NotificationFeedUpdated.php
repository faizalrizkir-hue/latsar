<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationFeedUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ?string $elementSlug = null,
        public readonly ?string $subtopicSlug = null,
        public readonly ?int $notificationId = null,
        public readonly int $occurredAt = 0
    ) {
    }

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('notifications.all')];
        $elementSlug = trim((string) $this->elementSlug);
        if ($elementSlug !== '') {
            $channels[] = new PrivateChannel('notifications.element.'.$elementSlug);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'notification.feed.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'element_slug' => $this->elementSlug,
            'subtopic_slug' => $this->subtopicSlug,
            'notification_id' => $this->notificationId,
            'occurred_at' => $this->occurredAt > 0 ? $this->occurredAt : time(),
        ];
    }
}
