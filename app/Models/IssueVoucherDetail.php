<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueVoucherDetail extends Model
{
    protected $guarded = [];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }
    
    public function issueVoucher()
    {
        return $this->belongsTo(IssueVoucher::class, 'issue_voucher_id');
    }
}
