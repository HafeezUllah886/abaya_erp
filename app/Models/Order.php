<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customer_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
