@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    My Cart
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Detail</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody class="product_list">
                        @foreach($cartItems as $item)
                            <tr data-id="{{ $item->productSku->id }}">
                                <td>
                                    <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
                                </td>
                                <td class="product_info">
                                    <div class="preview">
                                        <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                                            <img src="{{ $item->productSku->product->image_url }}">
                                        </a>
                                    </div>
                                    <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
                                        <span class="product_title">
                                            <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                                                {{ $item->productSku->product->title }}
                                            </a>
                                        </span>
                                        <span class="sku_title">
                                            {{ $item->productSku->title }}
                                        </span>
                                        @if(!$item->productSku->product->on_sale)
                                            <span class="warning">The product is unavailable</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="price">
                                        <b>$</b>{{ $item->productSku->price }}
                                    </span>
                                </td>
                                <td>
                                    <input type="text" class="form-control input-sm amount"
                                           @if(!$item->productSku->product->on_sale) disabled @endif
                                           name="amount" value="{{ $item->amount }}">
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-danger btn-remove">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        <form class="form-horizontal" role="form" id="order-form">
                            <div class="form-group">
                                <label class="control-label col-sm-3">
                                    Select Post Address
                                </label>
                                <div class="col-sm-9 col-md-7">
                                    <select class="form-control" name="address">
                                        @foreach($addresses as $address)
                                            <option value="{{ $address->id }}">
                                                {{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3">Notes:</label>
                                <div class="col-sm-9 col-md-7">
                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            {{--coupon start--}}
                            <div class="form-group">
                                <label class="control-label col-sm-3">CouponCode</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="coupon_code">
                                    <span class="help-block" id="coupon_desc"></span>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-success" id="btn-check-coupon">
                                        Check
                                    </button>
                                    <button type="button" class="btn btn-danger" style="display: none;" id="btn-cancel-coupon">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            {{--coupon start--}}
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-3">
                                    <button type="button" class="btn btn-primary btn-create-order">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {

            //Listen click event for 'remove' button
            $('.btn-remove').click(function () {

                //$(this) => jQuery of remove button
                //closest() will have its closest parent element of matched element which is remove button's <tr> tag
                //data('id') => value of data-id which is SKU id.
                var id = $(this).closest('tr').data('id');

                swal({
                    title:      "Remove this product?",
                    icon:       "warning",
                    buttons:    ['Cancel', 'OK'],
                    dangerMode: true,
                })
                    .then(function(willDelete) {

                        //when click 'OK', willDelete will be true, otherwise it is false
                        if (!willDelete) {
                            return;
                        }
                        axios.delete('/cart/' + id)
                            .then(function () {
                                location.reload();
                            })
                    });
            });

            //Listen 'select all'/'cancel' checkbox change event
            $('#select-all').change(function() {

                //take the checkbox status
                //prop() check whether the tag contains 'checked' attribute when this checkbox has been ticked
                var checked = $(this).prop('checked');

                //choose the checkbox which name=select and does not have disabled attribute
                //make sure the unavailable product cannot be selected -> add :not([disabled])_
                $('input[name=select][type=checkbox]:not([disabled])').each(function() {

                    //set its status same with target checkbox
                    $(this).prop('checked', checked);
                });
            });

            // Listen create new order event
            $('.btn-create-order').click(function () {

                // Create post data which contains remark and address ID user selected.
                var req = {
                    address_id: $('#order-form').find('select[name=address]').val(),
                    items: [],
                    remark: $('#order-form').find('textarea[name=remark]').val(),
                    coupon_code: $('input[name=coupon_code]').val();
                };

                // Traverse all tags which has data-id attribute in <table>. That is all product SKU in cart.
                $('table tr[data-id]').each(function () {

                    // Get checkbox in current line
                    var $checkbox = $(this).find('input[name=select][type=checkbox]');

                    // If unavailable or un-ticked then skip
                    if ($checkbox.prop('disabled') || !$checkbox.prop('checked')) {
                        return;
                    }

                    // Get input object in current line
                    var $input = $(this).find('input[name=amount]');

                    // If amount = 0 or non-numeric then skip
                    if ($input.val() == 0 || isNaN($input.val())) {
                        return;
                    }

                    // Push SKU ID and amount into application para array.
                    req.items.push({
                        sku_id: $(this).data('id'),
                        amount: $input.val(),
                    })
                });
                axios.post('{{ route('orders.store') }}', req)
                    .then(function (response) {
                        swal('Submit Success!', '', 'success')
                            .then(() => {
                                location.href = '/orders/' + response.data.id;
                            });
                    }, function (error) {
                        if (error.response.status === 422) {

                            // http status = 422 means input validation failed
                            var html = '<div>';
                            _.each(error.response.data.errors, function (errors) {
                                _.each(errors, function (error) {
                                    html += error+'<br>';
                                })
                            });
                            html += '</div>';
                            swal({content: $(html)[0], icon: 'error'})
                        } else {
                            // System error
                            swal('System Error', '', 'error');
                        }
                    });
            });

            //"Check" button click event
            $('#btn-check-coupon').click(function () {

                //get couponCode user entered
                var code = $('input[name=coupon_code]').val();

                //if no input
                if(!code) {
                    swal('Please enter Coupon Code: ', '', 'warning');
                    return;
                }

                //call check api
                axios.get('/coupon_codes/' + encodeURIComponent(code))
                    .then(function (response) {  // then() has a para which is a function. call it when applicate successfully
                        $('#coupon_desc').text(response.data.description); // output discount info
                        $('input[name=coupon_code]').prop('readonly', true); // disable input area
                        $('#btn-cancel-coupon').show(); // show 'Cancel' button
                        $('#btn-check-coupon').hide(); // hide 'Check' button
                    }, function (error) {
                        //if return code is 404, coupon does not exist
                        if(error.response.status === 404) {
                            swal('CouponCode does not exist.', '', 'error');
                        } else if (error.response.status === 403) {
                            //if 403, means some other conditions lost
                            swal(error.response.data.msg, '', 'error');
                        } else {
                            //other error
                            swal('System Error.', '', 'error');
                        }
                    })

            });

            //hidden button click event
            $('#btn-cancel-coupon').click(function () {
                $('#coupon_desc').text(''); // hidden discount info
                $('input[name=coupon_code]').prop('readonly', false);  // enable input area
                $('#btn-cancel-coupon').hide(); // hide 'Cancel' button
                $('#btn-check-coupon').show(); // show 'Check' button
            });
        });
    </script>
@endsection