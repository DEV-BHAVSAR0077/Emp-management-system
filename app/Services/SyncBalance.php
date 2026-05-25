<?php

namespace App\Services;

use App\Models\AgencyVendor;

class SyncBalance
{
    /**
     * Incrementally updates the balance for an AgencyVendor based on an operation.
     *
     * @param int|null $agencyVendorId
     * @param float $amount The amount to add or subtract.
     * @param string $type 'expense' or 'payment'
     * @param string $action 'add' or 'remove'
     * @param int|null $paymentType 0 or 1 for payments (1 = credit, 0 = debit)
     * @return float
     */
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

        if ($type === 'expense') {
            $balanceEffect = $amount;
        } elseif ($type === 'payment') {
            // payment_type: 1 = Credit, 0 = Debit
            // Old calculation logic: balance = totalExpenses - totalPayments
            // totalPayments was SUM(CASE WHEN payment_type = 1 THEN amount WHEN payment_type = 0 THEN -amount ELSE amount END)
            // If payment_type = 1 (Credit), totalPayments increases by $amount, so balance decreases by $amount.
            // If payment_type = 0 (Debit), totalPayments decreases by $amount, so balance increases by $amount.
            if ($paymentType === 0) {
                $balanceEffect = $amount;
            } else {
                $balanceEffect = -$amount;
            }
        }

        if ($action === 'remove') {
            $balanceEffect = -$balanceEffect;
        }

        $vendor->balance += $balanceEffect;
        $vendor->saveQuietly();

        return $vendor->balance;
    }
}
