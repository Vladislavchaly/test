<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Notifications\Staff\Orders\OrderCommentsCreated;
use App\Notifications\Staff\StaffNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OrderCommentNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private StaffNotification $staffNotification,
        private Carbon            $carbon,
        private Comment           $comment
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $newDateTime = $this->carbon->now()->subMinutes(15);
        $comments = $this->comment->selectRaw("count(comments.id) as count, orders.* as order, users.name")
            ->leftJoin('orders', 'comments.order_id', 'orders.id')
            ->leftJoin('users', 'comments.user_id', 'users.id')
            ->where('comments.created_at', '>', $newDateTime)
            ->groupBy('orders.id', 'users.id')
            ->get();

        foreach ($comments as $order) {
            $this->staffNotification->sendBulk(new OrderCommentsCreated($order, $order->count, $order->name));
        }
    }

}
