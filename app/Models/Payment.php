<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'user_id',
        'agency_vendor_id',
        'amount',
        'notes',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    // User who created this payment
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Agency/Vendor this payment was made to
    public function agencyVendor(): BelongsTo
    {
        return $this->belongsTo(AgencyVendor::class, 'agency_vendor_id')->withTrashed();
    }

}
