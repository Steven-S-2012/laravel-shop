<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * 字段名称	        描述	            类型	                加索引缘由
        id	            自增长ID	        unsigned int	    主键
        no	            订单流水号	    varchar	            唯一
        user_id	        下单的用户ID	    unsigned int	    外键
        address	        JSON收货地址	    text	            无
        total_amount	订单总金额	    decimal	            无
        remark	        订单备注	        text	            无
        paid_at	        支付时间	        datetime, null	    无
        payment_method	支付方式	        varchar, null	    无
        payment_no	    支付平台订单号    varchar, null	    无
        refund_status	退款状态	        varchar	            无
        refund_no	    退款单号	        varchar, null	    唯一
        closed	        订单是否已关闭    tinyint, default 0	无
        reviewed	    订单是否已评价    tinyint, default 0	无
        ship_status	    物流状态	        varchar	            无
        ship_data	    物流数据	        text, null	        无
        extra	        其他额外的数据    text, null	        无
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->unique();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('address');
            $table->decimal('total_amount');
            $table->text('remark')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_no')->nullable();
            $table->string('refund_status')->default(\App\Models\Order::REFUND_STATUS_PENDING);
            $table->string('refund_no')->nullable();
            $table->boolean('closed')->default(false);
            $table->boolean('reviewed')->default(false);
            $table->string('ship_status')->default(\App\Models\Order::SHIP_STATUS_PENDING);
            $table->text('ship_data')->nullable();
            $table->text('extra')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
