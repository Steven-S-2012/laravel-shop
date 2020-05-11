<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('size');
            $table->decimal('price', 10, 2);
            $table->decimal('price_m_au', 10, 2);
            $table->decimal('price_vip_au', 10, 2);
            $table->decimal('price_vvip_au', 10, 2);
            $table->decimal('price_rmb', 10, 2);
            $table->decimal('price_vip_rmb', 10, 2);
            $table->decimal('price_20_rmb', 10, 2);
            $table->decimal('price_vvip_rmb', 10, 2);
            $table->string('title_en');
            $table->integer('weight');
            $table->string('image');
            $table->string('category');
            $table->string('barcode');
            $table->decimal('gst');
            $table->decimal('cost');
            $table->decimal('real_cost');
            $table->string('barcode_family');
            $table->text('description');
            $table->integer('stock');
            $table->text('specialnote');
            $table->boolean('on_sale')->default(true);
            $table->float('rating')->default(5);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
