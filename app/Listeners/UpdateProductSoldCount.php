<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\OrderItem;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//asyncExecute if implements ShouldQueue
class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * Laravel default execute this function
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        //select corresponding order from event object
        $order = $event->getOrder();

        //pre-load product data
        $order->load('items.product');

        //traverse product in this order
        foreach ($order->items as $item) {
            $product   = $item->product;

            //calculate sales
            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) { //$query is the return from last query
                    $query->whereNotNull('paid_at'); //select order which is paid
                })->sum('amount');

            //update sales
            $product->update([
                'sold_count'  => $soldCount,
            ]);
        }
    }
}
