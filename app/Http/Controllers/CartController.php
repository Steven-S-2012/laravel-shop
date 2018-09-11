<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Http\Requests\AddCartRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();

        return view('cart.index', ['cartItems' => $cartItems]);

    }

    public function add(AddCartRequest $request)
    {
        $user    = $request->user();
        $skuId  = $request->input('sku_id');
        $amount = $request->input('amount');

        //check in the DB whether this product is already in the cart.
        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {

            //if existing, accumulate the amount
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {

            //or create a new product in cart
            $cart = new CartItem((['amount' => $amount]));
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return [];
    }

    public function remove(ProductSku $sku, Request $request)
    {
        $request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();

        return [];
    }
}
