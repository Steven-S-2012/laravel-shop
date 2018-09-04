<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id', 'title', 'size',
        'price', 'price_m_au', 'price_vip_au',
        'price_vvip_au', 'price_rmb', 'price_vip_rmb',
        'price_20_rmb', 'price_vvip_rmb', 'title_en',
        'weight', 'image', 'category',
        'barcode', 'gst', 'cost',
        'real_cost', 'barcode_family', 'description',
        'stock', 'specialnote', 'on_sale',
        'rating', 'sold_count', 'review_count'
    ];

    protected $casts = [
        'on_sale' => 'boolean',
    ];

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
}
