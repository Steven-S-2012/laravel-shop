<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        //$products = Product::query()->where('on_sale', true)->paginate(16);
        //create a query builder

        $builder = Product::query()->where('on_sale', true);

        //check if there is search paras, if has then send to $search
        //$search is the para for the search

        if ($search = $request->input('search', '')) {

            $like = '%'.$search.'%';

            //fuzzy search: product title,detail,SKU title,SKU description

            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        //check if submit Order Paras, then send to $order
        //$order controls the display sequence.

        if ($order = $request->input('order', '')) {

            //whether the value is ended by '_asc' or '_desc'

            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {

                //if the string start from 'price/sold_count/rating'

                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {

                    //build order para based on the passing value

                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate(16);

        return view('products.index', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }

    public function show(Product $product, Request $request)
    {
        //check whether product is on-sale, if no, throw the error.

        if(!$product->on_sale) {
            throw new InvalidRequestException('Product is not on-sale!');
        }

        $favored = false;

        //when didn't login, return null, otherwise return user object.
        if($user = $request->user()) {

            //select collected product whose id is same with current id
            //boolval() convert to boolean value.
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) // preload
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') // select rated
            ->orderBy('reviewed_at', 'desc') // group by desc
            ->limit(10) // first 10
            ->get();

        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews
        ]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
