<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class models extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(products::class);
    }
}
