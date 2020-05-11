<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class CartService
{
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();

        //query in DB that does that product in cart or not
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {

            //if in, add its amount
            $item->update([
                'amount' => $item->amount + $amount,
            ]);
        } else {
            //or create a new cart
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    public function remove($skuIds)
    {
        //paras can be single ID and ID array
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        Auth::user()->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
    }
}