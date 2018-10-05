<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use Carbon/Carbon;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        //check if order belongs to the current user
        $this->authorize('own', $order);

        //check whether order is paid or closed
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('Order Status Error');
        }

        //call alipay web payment
        return app('alipay')->web([
            'out_trade_no' => $order->no, //
            'total_amount' => $order->total_amount,
            'subject'      => 'Wallace Order No：'.$order->no,
        ]);
    }

    //front-end callback page
    public function alipayReturn()
    {
//        //check paras submitted correction
//        $data = app('alipay')->verify();
//        dd($data);

        try {
            app('alipay')->verify();
        } catch(\Exception $e) {
            return view('pages.error', ['msg' => 'Data Error!']);
        }

        return view('pages.success', ['msg' => 'Payment Success!']);
    }

    //server callback
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        //$data->out_trade_no get order No and search in DB
        $order = Order::where('no', $data->out_trade_no)->first();

        if (!$order) {
            return 'fail';
        }

        //if order paid
        if ($order->paid_at) {
            //return data to 支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => 'alipay',
            'payment_no'     => $data->trade_no,
        ]);

        return app('alipay')->success();
//        \Log::debug('Alipay notify', $data->all());
    }
}
