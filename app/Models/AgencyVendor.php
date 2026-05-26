<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyVendor extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_AGENCY = 0;
    public const TYPE_VENDOR = 1;

    public const TYPES = [
        self::TYPE_AGENCY => 'Agency',
        self::TYPE_VENDOR => 'Vendor',
    ];  

    protected $fillable = [
        'name',
        'type',
        'email',
        'phone_number',
        'contact_person',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance'          => 'decimal:2',
            'expenses_sum_amount' => 'decimal:2',
        ];
    }

    // Dynamically computed total from related expenses (use withSum in queries)
    public function getTotalExpensesAttribute(): string
    {
        return number_format(
            $this->expenses()->sum('amount'),
            2
        );
    }

    // Expenses associated with this agency/vendor
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'agency_vendor_id');
    }

    // Payments made to this agency/vendor
    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class, 'agency_vendor_id');
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(\App\Models\VendorLedger::class, 'vendor_id');
    }
}
