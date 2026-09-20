<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
