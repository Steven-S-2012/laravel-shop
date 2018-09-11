<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const REFUND_STATUS_PENDING     = 'pending';
    const REFUND_STATUS_APPLIED     = 'applied';
    const REFUND_STATUS_PROCESSING  = 'processing';
    const REFUND_STATUS_SUCCESS     = 'success';
    const REFUND_STATUS_FAILED      = 'failed';

    const SHIP_STATUS_PENDING       = 'pending';
    const SHIP_STATUS_DELIVERED     = 'delivered';
    const SHIP_STATUS_RECEIVED      = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING     => 'not refund',
        self::REFUND_STATUS_APPLIED     => 'applied refund',
        self::REFUND_STATUS_PROCESSING  => 'refunding',
        self::REFUND_STATUS_SUCCESS     => 'refund successful',
        self::REFUND_STATUS_FAILED      => 'refund failed',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => 'unshipped',
        self::SHIP_STATUS_DELIVERED => 'shipped',
        self::SHIP_STATUS_RECEIVED  => 'received',
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();

        //Listen model creation event, trigger before insert into DB
        static::creating(function ($model) {
            //if 'no' field is null
            if (!$model->no) {
                //call findAvailableNo, create 'no' field
                $model->no = static::findAvailableNo();
                //if failed, stop create order
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function findAvailableNo()
    {
        //prefix of order No
        $prefix = date('YmdHis');

        for ($i = 0; $i < 10; $i++) {
            //random 6 numbers
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            //check if exist
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }
}


