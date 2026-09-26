<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueVoucher extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(IssueVoucherDetail::class);
    }

    public function tailor()
    {
        return $this->belongsTo(accounts::class, 'tailor_id');
    }
}
