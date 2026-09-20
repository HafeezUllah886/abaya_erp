<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFulfillmentDetail extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
    
    public function fulfillment()
    {
        return $this->belongsTo(OrderFulfillment::class, 'order_fulfillment_id');
    }
}
