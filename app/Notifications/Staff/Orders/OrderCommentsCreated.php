<?php

namespace App\Notifications\Staff\Orders;

use App\Models\Order;
use App\Notifications\Staff\StaffNotification;

final class OrderCommentsCreated extends StaffNotification
{
    protected Order $order;

    /**
     * @param Order $order
     * @param $count
     * @param $firstName
     */
    public function __construct(Order $order, int $count, string $firstName)
    {
        $this->order = $order;
        $this->count = $count;
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    protected function content(): string
    {
        return "📩 {$this->firstName} has left {$this->count} messages";
    }

    /**
     * @return string|null
     */
    protected function externalLink(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    protected function eventData(): array
    {
        return [
            'id' => $this->order->id,
            'name' => 'order.comment.created',
            'model' => 'Order'
        ];
    }

    /**
     * Target user IDs who should receive the notification.
     *
     * @return array
     */
    public function receivers(): array
    {
        return [
            $this->targetSupporters(),
            $this->targetPM($this->order),
            $this->targetAdmin()
        ];
    }
}
