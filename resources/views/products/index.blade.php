@extends('layouts.app')
@section('title', 'Product List')

@section('content')
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-body">
                <!-- search function -->
                <div class="row">
                    <form action="{{ route('products.index') }}" class="form-inline search-form">
                        <input type="text" class="form-control input-sm" name="search" placeholder="Search">
                        <button class="btn btn-primary btn-sm">
                            Search
                        </button>
                        <select name="order" class="form-control input-sm pull-right">
                            <option value="">Arrangement</option>
                            <option value="price_asc">Price: Low -> High</option>
                            <option value="price_desc">Price: High -> Low</option>
                            <option value="sold_count_asc">Sale: Low -> High</option>
                            <option value="sold_count_desc">Sale: High -> Low</option>
                            <option value="rating_asc">Rating: Low -> High</option>
                            <option value="rating_desc">Rating: High -> Low</option>
                        </select>
                    </form>
                </div>
                <!--end of search-->
                <div class="row products-list">
                    @foreach($products as $product)
                        <div class="col-xs-3 product-item">
                            <div class="product-content">
                                <div class="top">
                                    <div class="img">
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}">
                                            <img src="{{ $product->image_url }}" alt="">
                                        </a>
                                    </div>
                                    <div class="price">
                                        <b>$</b>{{ $product->price }}
                                    </div>
                                    <div class="title">
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}">
                                            {{ $product->title }}
                                        </a>
                                    </div>
                                </div>
                                <div class="bottom">
                                    <div class="sold_count">
                                        <b>SOLD: </b><span>{{ $product->sold_count }}</span>
                                    </div>
                                    <div class="review_count">
                                        <b>VIEWS: </b><span>{{ $product->review_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pull-right">
                    {{ $products->appends($filters)->render() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scriptsAfterJs')
<script>

    var filters = {!! json_encode($filters) !!};
    $(document).ready(function () {

        $('.search-form input[name=search]').val(filters.search);
        $('.search-form select[name=order]').val(filters.order);
        $('.search-form select[name=order]').on('change', function() {

            $('.search-form').submit();
        });
    })
</script>
@endsection