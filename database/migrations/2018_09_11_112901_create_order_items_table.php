<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * 字段名称	          描述	            类型	        加索引缘由
        id	            自增长ID	        unsigned int	主键
        order_id	    所属订单ID	    unsigned int	外键
        product_id	    对应商品ID	    unsigned int	外键
        product_sku_id	对应商品SKU ID	unsigned int	外键
        amount	        数量	            unsigned int	无
        price	        单价	            decimal	        无
        rating	        用户打分	        unsigned int	无
        review	        用户评价	        text	        无
        reviewed_at	    评价时间	        timestamp, null	无
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('product_sku_id');
            $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');
            $table->unsignedInteger('amount');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('rating')->nullable();
            $table->text('review')->nullable();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
