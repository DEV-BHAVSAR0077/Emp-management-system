<?php

namespace App\Services;

use App\Models\AgencyVendor;

class SyncBalance
{
    public static function updateBalance(?int $agencyVendorId, float $amount, string $type, string $action, ?int $paymentType = null)
    {
        if (!$agencyVendorId) {
            return 0;
        }

        $vendor = AgencyVendor::find($agencyVendorId);
        if (!$vendor) {
            return 0;
        }

        $balanceEffect = 0;
        if ($type === 'expense' || $paymentType === 0) {
            $balanceEffect = $amount;
        } else {
            $balanceEffect = -$amount;
        }

        if ($action === 'remove') {
            $balanceEffect = -$balanceEffect;
        }

        $vendor->balance += $balanceEffect;
        $vendor->saveQuietly();

        return $vendor->balance;
    }
}
