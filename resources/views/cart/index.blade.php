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
        });
    </script>
@endsection