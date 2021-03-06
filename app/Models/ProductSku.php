<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = ['title', 'description', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('Stock decrease cannot be negative!');
        }

        return $this->newQuery()->where('id', $this->id)
            ->where('stock', '>=', $amount)
            ->decrement('stock', $amount);
    }

    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('Stock increase cannot be negative!');
        }
        $this->increment('stock', $amount);
    }
}
