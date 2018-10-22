<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Order No. : {{ $order->no }}</h3>
        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 10px">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default">
                    <i class="fa fa-list"></i> List
                </a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>Buyer:</td>
                <td>{{ $order->user->name }}</td>
                <td>Paid:</td>
                <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td>Payment Method：</td>
                <td>{{ $order->payment_method }}</td>
                <td>Payment No.：</td>
                <td>{{ $order->payment_no }}</td>
            </tr>
            <tr>
                <td>Post Address</td>
                <td colspan="3">{{ $order->address['address'] }} {{ $order->address['zip'] }} {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
            </tr>
            <tr>
                <td rowspan="{{ $order->items->count() + 1 }}">ProductList</td>
                <td>Product</td>
                <td>Price</td>
                <td>Amount</td>
            </tr>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->title }} {{ $item->productSku->title }}</td>
                    <td><b>$</b>{{ $item->price }}</td>
                    <td>{{ $item->amount }}</td>
                </tr>
            @endforeach
            <tr>
                <td>Order Price：</td>
                {{--<td colspan="3"><b>$</b>{{ $order->total_amount }}</td>--}}
                <td><b>$</b>{{ $order->total_amount }}</td>
                <td>Shipment</td>
                <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
            </tr>
            {{--shipment--}}
            {{--if did not ship, show delivery form--}}
            @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
            <tr>
                <td colspan="4">
                    <form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">
                        {{--csrf token--}}
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">
                            <label for="express_company" class="control-label">Delivery Company</label>
                            <input type="text" id="express_company" name="express_company" value=""
                                   class="form-control" placeholder="Delivery Company">
                            @if($errors->has('express_company'))
                                @foreach($errors->get('express_company') as $msg)
                                    <span class="help-block">{{ $msg }}</span>
                                @endforeach
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('express_no') ? 'has-error' : '' }}">
                            <label for="express_no" class="control-label">Delivery No.</label>
                            <input type="text" id="express_no" name="express_no" value=""
                                   class="form-control" placeholder="Delivery No.">
                            @if($errors->has('express_no'))
                                @foreach($errors->get('express_no') as $msg)
                                    <span class="help-block">{{ $msg }}</span>
                                @endforeach
                            @endif
                        </div>
                        <button type="submit" class="btn btn-success" id="ship-btn">Delivery</button>
                    </form>
                </td>
            </tr>
            @else
            {{--or show delivery company and No.--}}
            <tr>
                <td>Delivery Companay：</td>
                <td>{{ $order->ship_data['express_company'] }}</td>
                <td>Delivery No. ：</td>
                <td>{{ $order->ship_data['express_no'] }}</td>
            </tr>
            @endif
            @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
            <tr>
                <td>Refund Status:</td>
                <td colspan="2">
                    {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }},
                    Reason: {{ $order->extra['refund_reason'] }}
                </td>
                <td>
                    {{--if refund applicated, show 'processing' button--}}
                    @if($order->refund_status === \App\Models\Order::REFUND_STATUS_APPLIED)
                        <button class="btn btn-sm btn-success" id="btn-refund-agree">Agree</button>
                        <button class="btn btn-sm btn-danger" id="btn-refund-disagree">Disagree</button>
                    @endif
                </td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        //disagree event
        $('#btn-refund-disagree').click(function() {
            //swal lv 1
            swal({
                title: 'Reject Reason',
                type: 'input',
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonText: "Submit",
                cancelButtonText: "Cancel",
            }, function(inputValue) {
                // if click 'Submit', inputValue = false
                // === check whether click 'cancel' or no input
                if (inputValue === false) {
                    return;
                }
                if (!inputValue) {
                    swal('Need a reason!', '', 'error')
                    return;
                }

                // Laravel-Admin use ajax instead of axios
                $.ajax({
                    url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
                    type: 'POST',
                    data: JSON.stringify({
                        agree: false,
                        reason: inputValue,
                        //CSRF Token
                        //Laravel-Admin page can get CSRF Token through LA.token
                        _token: LA.token,
                    }),
                    contentType: 'application/json', //application data type is JSON
                    success: function (data) {
                        swal({
                            title: 'Processing Success!',
                            type: 'success'
                        }, function() {
                            //reload page if click button on swal
                            location.reload();
                        });
                    }
                });
            };
        });
    });
</script>