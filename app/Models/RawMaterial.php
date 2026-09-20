<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function stock()
    {
        return $this->hasMany(stock::class, 'item_id', 'id')->where('item_type', 'raw_material');
    }
}
