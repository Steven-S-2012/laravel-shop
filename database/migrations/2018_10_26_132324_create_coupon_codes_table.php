<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * 字段名称	        描述	                            类型	                    加索引缘由
        id	        自增长ID	                            unsigned int	        主键
        name	    优惠券的标题	                        varchar	                无
        code	    优惠码，用户下单时输入	                varchar	                唯一
        type	    优惠券类型，支持固定金额和百分比折扣	    varchar	                无
        value	    折扣值，根据不同类型含义不同	        decimal	                无
        total	    全站可兑换的数量	                    unsigned int	        无
        used	    当前已兑换的数量	                    unsigned int, default 0	无
        min_amount	使用该优惠券的最低订单金额	            decimal	                无
        not_before	在这个时间之前不可用	                datetime, null	        无
        not_after	在这个时间之后不可用	                datetime, null	        无
        enabled	    优惠券是否生效	                    tinyint	                无
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type');
            $table->decimal('value');
            $table->unsignedInteger('total');
            $table->unsignedInteger('used')->default(0);
            $table->decimal('min_amount', 10, 2);
            $table->datetime('not_before')->nullable();
            $table->datetime('not_after')->nullable();
            $table->boolean('enabled');
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
        Schema::dropIfExists('coupon_codes');
    }
}
