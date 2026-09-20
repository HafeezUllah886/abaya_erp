<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function details()
    {
        return $this->hasMany(OrderFulfillmentDetail::class);
    }
}
