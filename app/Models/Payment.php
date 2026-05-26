<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_DEBIT  = 0;
    public const TYPE_CREDIT = 1;

    public const TYPES = [
        self::TYPE_DEBIT  => 'Debit',
        self::TYPE_CREDIT => 'Credit',
    ];

    protected $fillable = [
        'user_id',
        'agency_vendor_id',
        'amount',
        'payment_type',
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
    public function ledgers()
    {
        return $this->morphMany(VendorLedger::class, 'loggable');
    }
}
