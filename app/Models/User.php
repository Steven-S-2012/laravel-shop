<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email_verified'
    ];

    /**
     * Notify email_verified is a boolean attribute
     */
    protected $casts = [
        'email_verified'=>'boolean',
    ];

    //make relationship with UserAddress table (User <-> UserAddress || 1 <-> many)
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
