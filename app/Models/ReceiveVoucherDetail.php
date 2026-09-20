<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveVoucherDetail extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }
    
    public function receiveVoucher()
    {
        return $this->belongsTo(ReceiveVoucher::class, 'receive_voucher_id');
    }
}
