<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
//use App\Services\CartService;
//use Carbon\Carbon;
//use App\Models\ProductSku;
//use App\Jobs\CloseOrder;

class OrdersController extends Controller
{
    //自动解析注入CartService Class
    public function store (OrderRequest $request, OrderService $orderService)
    {
        $user    = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));

//        //start a DB transaction
//        $order = \DB::transaction(function () use ($user, $request, $cartService) {
//            $address = UserAddress::find($request->input('address_id'));
//
//            //update last usage time for this address
//            $address->update(['last_used_at' => Carbon::now()]);
//
//            //create an order
//            $order = new Order([
//                'address'      => [ // insert address info into order
//                    'address'       => $address->full_address,
//                    'zip'           => $address->zip,
//                    'contact_name'  => $address->contact_name,
//                    'contact_phone' => $address->contact_phone,
//                ],
//                'remark'       => $request->input('remark'),
//                'total_amount' => 0,
//            ]);
//
//            //link order to the user
//            $order->user()->associate($user);
//
//            //write into DB
//            $order->save();
//
//            $totalAmount = 0;
//            $items       = $request->input('items');
//
//            //traverse SKU user submitted
//            foreach ($items as $data) {
//                $sku  = ProductSku::find($data['sku_id']);
//
//                //create an OrderItem and link to this order
//                $item = $order->items()->make([
//                    'amount' => $data['amount'],
//                    'price'  => $sku->price,
//                ]);
//                $item->product()->associate($sku->product_id);
//                $item->productSku()->associate($sku);
//                $item->save();
//                $totalAmount += $sku->price * $data['amount'];
//                if ($sku->decreaseStock($data['amount']) <= 0) {
//                    throw new InvalidRequestException('Out of stock!');
//                }
//            }
//
//            //update order amount
//            $order->update(['total_amount' => $totalAmount]);
//
//            //remove ordered product from cart
//            $skuIds = collect($request->input('items'))->pluck('sku_id')->all();
//            $cartService->remove($skuIds);
////            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
//
//            return $order;
//
//        });
//
//        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
//
//        return $order;
    }

    public function index(Request $request)
    {
        $orders = Order::query()

            //with() to avoid N+1 prob,
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function received(Order $order, Request $request)
    {
        //check authorization
        $this->authorize('own', $order);

        //check whether it is shipped
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        //Update shipment status
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        //back to last page
        //return redirect()->back();
        return $order;
    }
}
