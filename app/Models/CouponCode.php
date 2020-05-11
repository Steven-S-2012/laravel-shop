<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\CouponCodeUnavailableException;

class CouponCode extends Model
{
    //define coupon style by using const
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED    => 'fixed price',
        self::TYPE_PERCENT  => 'percent',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    //define date type
    protected $dates = ['not_before', 'not_after'];

    protected $appends = ['description'];

    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = 'Over '.str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str.'have '.str_replace('.00', '', $this->value).'%';
        }

        return $str.'discount'.str_replace('.00', '', $this->value);
    }

    public static function findAvailableCode($length = 16)
    {
        do {
            //create random string with fixed length and upper case
            $code = strtoupper(Str::random($length));
        //if exists then keep loop
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    public function checkAvailable(User $user, $orderAmount = null)
    {
        if (!$this->enabled) {
            throw new CouponCodeUnavailableException('Coupon does not exist!');
        }

        if ($this->total - $this->used <= 0) {
            throw new CouponCodeUnavailableException('Coupon has been used!');
        }

        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeUnavailableException('Coupon is not available!');
        }

        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeUnavailableException('Coupon expired!');
        }

        if (!is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new CouponCodeUnavailableException('Order amount is not enough for coupon!');
        }

        $used = Order::where('user_id', $user->id)
            ->where('coupon_code_id', $this->id)
            ->where(function($query) {
                $query->where(function($query) {
                    $query->whereNull('paid_at')
                        ->where('closed', false);
                })->orWhere(function($query) {
                    $query->whereNotNull('paid_at')
                        ->where('refund_status', '!=', Order::REFUND_STATUS_SUCCESS);
                });
            })
            ->exists();
        if ($used) {
            throw new CouponCodeUnavailableException('This coupon is used!');
        }
    }

    public function getAdjustedPrice($orderAmount)
    {
        // if discount type is fixed amount
        if ($this->type === self::TYPE_FIXED) {
            // make sure min amount of order is over 0.01
            return max(0.01, $orderAmount - $this->value);
        }

        return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
    }

    public function changeUsed($increase = true)
    {
        // pass true means increase usage, otherwise means decrease
        if ($increase) {
            // check if usage is over total stock
            return $this->newQuery()->where('id', $this->id)->where('used', '<', $this->total)->increment('used');
        } else {
            return $this->decrement('used');
        }
    }
}
