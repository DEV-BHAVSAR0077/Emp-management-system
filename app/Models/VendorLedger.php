<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorLedger extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function loggable()
    {
        return $this->morphTo();
    }

    public function vendor()
    {
        return $this->belongsTo(AgencyVendor::class, 'vendor_id');
    }
}
