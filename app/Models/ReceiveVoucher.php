<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveVoucher extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(ReceiveVoucherDetail::class);
    }

    public function issueVoucher()
    {
        return $this->belongsTo(IssueVoucher::class);
    }
}
