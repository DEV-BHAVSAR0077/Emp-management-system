<?php

namespace App\Services;

use App\Models\VendorLedger;
use Illuminate\Database\Eloquent\Model;

class VendorLedgerService
{
    public static function addEntry(
        Model $loggable,
        int $vendorId,
        float $amount,
        string $type,
        float $runningBalance,
        string $systemNote,
        ?int $paymentType = null
    ) {
        $debit = 0;
        $credit = 0;

        // Determine if it's a debit or credit entry
        if ($type === 'expense' || $paymentType === 0) {
            $debit = $amount;
        } else {
            $credit = $amount;
        }

        VendorLedger::create([
            'vendor_id'     => $vendorId,
            'loggable_id'   => $loggable->id,
            'loggable_type' => get_class($loggable),
            'log_at'        => $loggable->payment_date ?? $loggable->expense_date ?? now(),
            'debit'         => $debit,
            'credit'        => $credit,
            'balance'       => -$runningBalance,
            'system_note'   => $systemNote,
        ]);
    }

    public static function addUpdateEntry(
        Model $loggable,
        int $vendorId,
        float $oldAmount,
        string $oldType,
        ?int $oldPaymentType,
        float $newAmount,
        string $newType,
        ?int $newPaymentType,
        float $runningBalance,
        string $systemNote
    ) {
        $debit = 0;
        $credit = 0;

        // Reverse old amount
        if ($oldType === 'expense' || $oldPaymentType === 0) {
            $credit += $oldAmount; // Old was Debit, reverse by putting in Credit
        } else {
            $debit += $oldAmount;  // Old was Credit, reverse by putting in Debit
        }

        // Apply new amount
        if ($newType === 'expense' || $newPaymentType === 0) {
            $debit += $newAmount;
        } else {
            $credit += $newAmount;
        }

        VendorLedger::create([
            'vendor_id'     => $vendorId,
            'loggable_id'   => $loggable->id,
            'loggable_type' => get_class($loggable),
            'log_at'        => $loggable->payment_date ?? $loggable->expense_date ?? now(),
            'debit'         => $debit,
            'credit'        => $credit,
            'balance'       => -$runningBalance,
            'system_note'   => $systemNote,
        ]);
    }

    public static function addRemoveEntry(
        Model $loggable,
        int $vendorId,
        float $amount,
        string $type,
        float $runningBalance,
        string $systemNote,
        ?int $paymentType = null
    ){
        $debit = 0;
        $credit = 0;

        // Reverse old amount
        if ($type === 'expense' || $paymentType === 0) {
            $credit = $amount; // Old was Debit, reverse by putting in Credit
        } else {
            $debit = $amount;  // Old was Credit, reverse by putting in Debit
        }

        VendorLedger::create([
            'vendor_id'     => $vendorId,
            'loggable_id'   => $loggable->id,
            'loggable_type' => get_class($loggable),
            'log_at'        => $loggable->payment_date ?? $loggable->expense_date ?? now(),
            'debit'         => $debit,
            'credit'        => $credit,
            'balance'       => -$runningBalance,
            'system_note'   => $systemNote,
        ]);
    }
        
}
