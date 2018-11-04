<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\CouponCode;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\CouponCodeUnavailableException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items, CouponCode $coupon = null)
    {
        //if passed coupon, check if it is available
        if ($coupon) {
            // need to calculate total order amount
            $coupon->checkAvailable($user);
        }

        //create a DB transaction
        $order = \DB::transaction(function () use ($user, $address, $remark, $items, $coupon) {

            //update last time of using this address
            $address->update(['last_used_at' => Carbon::now()]);

            //create an order
            $order = new Order([
                'address'      => [ // put address info into created order
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $remark,
                'total_amount' => 0,
            ]);

            // set the link between user and order
            $order->user()->associate($user);

            //write into DB
            $order->save();

            $totalAmount = 0;

            //traverse all SKU submitted by user
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                //create an OrderItem and link it to the order
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('Not Enough Stock');
                }
            }

            if ($coupon) {
                // here has order total amount, then check if match coupon rules
                $coupon->checkAvailable($user, $totalAmount);

                // update order amount as discounted
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);

                // link order and coupon
                $order->couponCode()->associate($coupon);

                // add coupon usage, check return value
                if ($coupon->changeUsed() <= 0) {
                    throw new CouponCodeUnavailableException('Coupon has been used!');
                }
            }

            //update total price of order
            $order->update(['total_amount' => $totalAmount]);

            //remove ordered product from cart
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        //dispatch()
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}