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
        $oldNet = ($oldType === 'expense' || $oldPaymentType === 0) ? $oldAmount : -$oldAmount;
        $newNet = ($newType === 'expense' || $newPaymentType === 0) ? $newAmount : -$newAmount;

        $difference = $newNet - $oldNet;

        // If no change in net amount, do not create an entry
        if ($difference == 0) {
            return;
        }

        $debit = 0;
        $credit = 0;

        if ($difference > 0) {
            $debit = $difference;
        } else {
            $credit = -$difference;
        }

        $entityName = ucfirst($newType);
        $customNote = $systemNote;

        if ($newAmount > $oldAmount) {
            $customNote = $entityName . ' Increased';
        } elseif ($newAmount < $oldAmount) {
            $customNote = $entityName . ' Reduced';
        } elseif ($oldType !== $newType || $oldPaymentType !== $newPaymentType) {
            $customNote = $entityName . ' Updated';
        }

        VendorLedger::create([
            'vendor_id'     => $vendorId,
            'loggable_id'   => $loggable->id,
            'loggable_type' => get_class($loggable),
            'log_at'        => $loggable->payment_date ?? $loggable->expense_date ?? now(),
            'debit'         => $debit,
            'credit'        => $credit,
            'balance'       => -$runningBalance,
            'system_note'   => $customNote,
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
