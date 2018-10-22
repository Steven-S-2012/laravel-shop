@extends('layouts.app')
@section('title', 'Order Details')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Order Detail</h4>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Product Info</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Amount</th>
                            <th class="text-right item-amount">Sum</th>
                        </tr>
                        </thead>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td class="product-info">
                                    <div class="preview">
                                        <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                                            <img src="{{ $item->product->image_url }}">
                                        </a>
                                    </div>
                                    <div>
                                        <span class="product-title">
                                           <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                        </span>
                                        <span class="sku-title">{{ $item->productSku->title }}</span>
                                    </div>
                                </td>
                                <td class="sku-price text-center vertical-middle"><b>$</b>{{ $item->price }}</td>
                                <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
                                <td class="item-amount text-right vertical-middle">
                                    <b>$</b>{{ number_format($item->price * $item->amount, 2, '.', '') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr><td colspan="4"></td></tr>
                    </table>
                    <div class="order-bottom">
                        <div class="order-info">
                            <div class="line">
                                <div class="line-label">Post Address:</div><div class="line-value">{{ join(' ', $order->address) }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">Order Remark:</div><div class="line-value">{{ $order->remark ?: '-' }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">Order Number:</div><div class="line-value">{{ $order->no }}</div>
                            </div>
                            {{--delivery status--}}
                            <div class="line">
                                <div class="line-label">Delivery Status:</div>
                                <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
                            </div>
                            {{--if delivery status then dipaly--}}
                            @if($order->ship_data)
                                <div class="line">
                                    <div class="line-label">Delivery Details:</div>
                                    <div class="line-value">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</div>
                                </div>
                            @endif
                            {{--order paid and refund status is not refund, then display refund info --}}
                            @if($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
                                <div class="line">
                                    <div class="line-label">Refund Status:</div>
                                    <div class="line-value">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</div>
                                </div>
                                <div class="line">
                                    <div class="line-label">Refund Reason:</div>
                                    <div class="line-value">{{ $order->extra['refund_reason'] }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="order-summary text-right">
                            <div class="total-amount">
                                <span>Total Amount：</span>
                                <div class="value"><b>$</b>{{ $order->total_amount }}</div>
                            </div>
                            <div>
                                <span>Order Status：</span>
                                <div class="value">
                                    @if($order->paid_at)
                                        @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                            PAID
                                        @else
                                            {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                        @endif
                                    @elseif($order->closed)
                                        CLOSED
                                    @else
                                        NOT PAID
                                    @endif
                                </div>
                            </div>
                            @if(isset($order->extra['refund_disagree_reason']))
                                <div>
                                    <span>Reject Reason:</span>
                                    <div class="value">{{ $order->extra['refund_disagree_reason'] }}</div>
                                </div>
                            @endif
                            <!--payment button -->
                            @if(!$order->paid_at && !$order->closed)
                                <div class="payment-buttons">
                                    <a class="btn btn-primary btn-sm" href="{{ route('payment.alipay', ['order' => $order->id]) }}">
                                        AliPay
                                    </a>
                                    <button class="btn btn-sm btn-success" id='btn-wechat'>WeChat</button>
                                    {{--<a class="btn btn-success btn-sm" href="{{ route('payment.wechat', ['order' => $order->id]) }}">--}}
                                        {{--WeChat--}}
                                    {{--</a>--}}
                                </div>
                            @endif
                            {{--if shipment status is shipped then show confirm button--}}
                            @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
                                <div class="receive-button">
                                    {{--<form method="post" action="{{ route('orders.received', [$order->id]) }}">--}}
                                        {{--{{ csrf_field() }}--}}
                                        {{--<button type="submit" class="btn btn-sm btn-success">Confirm</button>--}}
                                    {{--</form>--}}
                                    {{--double confirm receive--}}
                                    <button type="button" id="btn-receive" class="btn btn-sm btn-success">Confirm</button>
                                </div>
                            @endif
                            {{--if order paid and refund status is not refund then show refund button--}}
                            @if($order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                <div class="refund-button">
                                    <button class="btn btn-sm btn-danger" id="btn-apply-refund">Refund</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scriptsAfterJs')
    <script>
        $(document).ready(function() {
            //wechat payment event
            $('#btn-wechat').click(function() {
                swal({
                    //content can be a DOM element,
                    //create a img tag by jQuery dynamiclly
                    //[0] is target element
                    content: $('<img src="{{ route('payment.wechat', ['order' => $order->id]) }}" />')[0],

                    //buttons para show button text
                    buttons: ['Close', 'Paid'],
                })
                    .then(function(result) {
                        //if click 'paid', reload page
                        if (result) {
                            location.reload();
                        }
                    })
            });

            //Delivery confirmation click event
            $('#btn-receive').click(function() {
               //confirmation box
               swal({
                   title: "Delivery Confirmation",
                   icon: "warning",
                   buttons: true,
                   dangerMode: true,
                   buttons: ['Cancel', 'Confirm'],
               })
                   .then(function(ret) {
                       //if cancel (ret)
                       if (!ret) {
                           return;
                       }
                       //ajax confirm action
                       axios.post('{{ route('orders.received', [$order->id]) }}')
                           .then(function () {
                               //reload page
                               location.reload();
                           })
                   });
            });

            //refund button click event
            $('#btn-apply-refund').click(function () {
                swal({
                    text: 'Enter refund reason:',
                    content: 'input',
                }).then(function (input) {
                    //if click button in pop-up box, trigger this function
                    if(!input) {
                        swal('Must has a refund reason!', '', 'error');
                        return;
                    }

                    //applicate refund api
                    axios.post('{{ route('orders.apply_refund', [$order->id]) }}', {reason: input})
                        .then(function () {
                            swal('Refund Success!', '', 'success').then(function () {
                                //reload page when click button
                                location.reload();
                            });
                        });
                });
            });
        });
    </script>
@endsection