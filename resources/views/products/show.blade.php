@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-body product-info">
                <div class="row">
                    <div class="col-sm-5">
                        <img class="cover" src="{{ $product->image_url }}" alt="">
                    </div>
                    <div class="col-sm-7">
                        <div class="title">
                            {{ $product->title }}
                        </div>
                        <div class="price">
                            <label>Price: </label><em>$</em><span>{{ $product->price }}</span>
                        </div>
                        <div class="sales_and_reviews">
                            <div class="sold_count">
                                <b>Sales: </b><span class="count">{{ $product->sold_count }}</span>
                            </div>
                            <div class="review_count">
                                <b>Reviews: </b><span class="count">{{ $product->review_count }}</span>
                            </div>
                            <div class="rating" title="Rating {{ $product->rating }}">
                                <b>Rating: </b><span class="count">
                                    {{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}
                                </span>
                            </div>
                        </div>
                        <div class="skus">
                            <label>Selection:</label>
                            <div class="btn-group" data-toggle="buttons">
                                @foreach($product->skus as $sku)
                                    <label
                                            class="btn btn-default sku-btn"
                                            data-price="{{ $sku->price }}"
                                            data-stock="{{ $sku->stock }}"
                                            data-toggle="tooltip"
                                            title="{{ $sku->description }}"
                                            data-placement="bottom">
                                        <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="cart_amount">
                            <label>Amount:  </label>
                            <input type="text" class="form-control input-sm" value="1">
                            <span class="stock"></span>
                        </div>
                        <div class="buttons">
                            @if($favored)
                                <button class="btn btn-danger btn-disfavor">Cancel Collection</button>
                            @else
                                <button class="btn btn-success btn-favor">❤ Collect</button>
                            @endif
                            <button class="btn btn-primary btn-add-to-cart">Add to cart</button>
                        </div>
                    </div>
                </div>
                <div class="product-detail">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab">Show Detail</a></li>
                        <li role="presentation"><a href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab">Reviews</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
                            {!! $product->description !!}
                        </div>
                        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {

            $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});

            $('.sku-btn').click(function () {
                $('.product-info .price span').text($(this).data('price'));
                $('.product-info .stock').text('Stock：' + $(this).data('stock'));
            });

            // Listen collect btn click event

            $('.btn-favor').click(function () {

                //post a ajax application, require route() from backend to create a url
                axios.post('{{ route('products.favor', ['product' => $product->id]) }}')
                    .then(function () { // if success
                        swal('Operation Success!', '', 'success')
                            .then(function (){
                                location.reload();
                        });
                    }, function(error) { // if failed
                        // return 401 => did not login
                        if (error.response && error.response.status === 401) {
                            swal('Please Login!', '', 'error');
                        } else if (error.response && error.response.data.msg) {
                            // if other error message, print out
                            swal(error.response.data.msg, '', 'error');
                        }  else {
                            // otherwise system error
                            swal('System Error!', '', 'error');
                        }
                    });
            });

            $('.btn-disfavor').click(function () {
                axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
                    .then(function () {
                        swal('Operation Success!', '', 'success')
                            .then(function () {
                                location.reload();
                            });
                    });
            });

            //Add to cart click event
            $('.btn-add-to-cart').click(function () {

                //call the api of add to cart
                axios.post('{{ route('cart.add') }}', {
                    sku_id: $('label.active input[name=skus]').val(),
                    amount: $('.cart_amount input').val(),
                })
                    // stay at detail page after warning sign
                    // .then(function () {
                    //     //if success run this callback function
                    //     swal('Add successfully!', '', 'success');
                    // }, function (error) {

                    .then(function () {
                        //if success run this callback function
                        swal('Add successfully!', '', 'success')
                            .then(function() {
                                location.href = '{{ route('cart.index') }}';
                            });
                    }, function (error) {
                        //if failed run this callback
                        if (error.response.status === 401) {

                            //http status = 401 means unlogin
                            swal('Please Login!', '', 'error');

                        } else if (error.response.status === 422) {

                            //http status = 422 means user input check is failed
                            var html = '<div>';
                            _.each(error.response.data.errors, function (errors) {
                                _.each(errors, function (error) {
                                    html += error + '<br>';
                                })
                            });
                            html += '</div>';
                            swal({content: $(html)[0], icon: 'error'})

                        } else {

                            //otherwise system error
                            swal('System Error!', '', 'error');
                        }
                    })
            });
        })
    </script>
@endsection