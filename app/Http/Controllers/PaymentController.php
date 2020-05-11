<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use App\Events\OrderPaid;

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

        //trigger event
        $this->afterPaid($order);

        return app('alipay')->success();
//        \Log::debug('Alipay notify', $data->all());
    }

    public function payByWechat(Order $order, Request $request)
    {
        //authorization check
        $this->authorize('own', $order);

        //check order status
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('Order Status Error!');
        }

        //call 'scan' function for wechat payment
        $wechatOrder = app('wechat_pay')->scan([
            'out_trade_no'  => $order->no,
            'total_fee'     => $order->total_amount * 100,  //wechat calculation unit is cend
            'body'          => 'Wallace Order No：'.$order->no,
        ]);

        //Construct function para for QrCode
        $qrCode = new QrCode($wechatOrder->code_url);

        //output orcode picture as string together with response type
        return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

    public function wechatNotify()
    {
        //check callback paras
        $data = app('wechat_pay')->verify();

        //find that order
        $order = Order::where('no', $data->out_trade_no)->first();

        //if does not exist
        if(!$order) {
            return 'fail';
        }

        //if paid
        if ($order->paid_at) {

            return app('wechat_pay')->success();
        }

        //mark as paid
        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => 'wechat',
            'payment_no'     => $data->transaction_id,
        ]);

        //trigger event
        $this->afterPaid($order);

        return app('wechat_pay')->success();
    }

    //initialize event
    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }

    public function wechatRefundNotify(Request $request)
    {
        // failed info for wechat
        $failXml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>';
        $data = app('wechat_pay')->verify(null, true);

        // if order does not exist,
        if(!$order = Order::where('no', $data['out_trade_no'])->first()) {
            return $failXml;
        }

        if ($data['refund_status'] === 'SUCCESS') {
            //refund success, update refund status
            $order->update([
                'refund_status' => Order::REFUND_STATUS_SUCCESS,
            ]);
        } else {
            //if failed, save error into extra field and marked as failed
            $extra = $order->extra;
            $extra['refund_failed_code'] = $data['refund_status'];
            $order->update([
                'refund_status' => Order::REFUND_STATUS_FAILED,
            ]);
        }

        return app('wechat_pay')->success();
    }
}
