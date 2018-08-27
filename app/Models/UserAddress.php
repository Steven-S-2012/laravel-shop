<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    protected $date = ['last_used_at'];

    //make relationship with User table (User <-> UserAddress || 1 <-> many)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //create an Accessor to access full_address value
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
