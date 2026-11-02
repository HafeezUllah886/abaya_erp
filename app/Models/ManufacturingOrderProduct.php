<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingOrderProduct extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
}
