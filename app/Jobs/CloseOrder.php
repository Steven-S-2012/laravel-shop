<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

class CloseOrder implements ShouldQueue
{
    //mean this class will be put in the queue instead of trigger immediately.
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Order $order, $delay)
    {
        $this->order = $order;

        //delay() :  set delay time (s)
        $this->delay($delay);
    }

    /**
     * Execute the job.
     * Define job logic.
     * Call handle() when queue processor pick the job
     * @return void
     */
    public function handle()
    {
        //check whether corresponding order paid
        //if so, exit directly without close order
        if ($this->order->paid_at) {
            return;
        }

        // DB transaction sql
        \DB::transaction(function() {

            //set 'closed' = true, means close order
            $this->order->update(['closed' => true]);

            //traverse the SKU in the order, add the amount in SKU stock
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
