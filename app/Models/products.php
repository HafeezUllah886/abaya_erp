<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFinished($query)
    {
        return $query->where('type', 'Finished Abaya');
    }

    public function scopeRawMaterial($query)
    {
        return $query->where('type', 'Raw Material');
    }
}
