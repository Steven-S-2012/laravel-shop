<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = factory(\App\Models\Product::class, 30)->create();
        foreach ($products as $product) {
            //create 3 SKU, 'product_id' for each SKU refers the 'id' of the product in this loop.
            $skus = factory(\App\Models\ProductSku::class, 3)->create(['product_id' => $product->id]);
            //set the product's price with the lowest SKU price.
            $product->update(['price' => $skus->min('price')]);
        }
    }
}
