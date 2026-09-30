<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingOrder extends Model
{
    protected $guarded = [];

    public function tailor()
    {
        return $this->belongsTo(accounts::class, 'tailor_id');
    }

    public function products()
    {
        return $this->hasMany(ManufacturingOrderProduct::class);
    }

    public function materials()
    {
        return $this->hasMany(ManufacturingOrderMaterial::class);
    }
}
