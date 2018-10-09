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
                <td rowspan="{{ $order->items->count() + 1 }}">商品列表</td>
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
                <td colspan="3"><b>$</b>{{ $order->total_amount }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>