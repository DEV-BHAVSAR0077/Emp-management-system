<?php

namespace App\Observers;

use App\Models\AgencyVendor;
use App\Models\Expense;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * Recalculate and persist the balance for a given agency_vendor_id.
     * Balance = total expenses − total payments (i.e. outstanding amount).
     */
    private function syncBalance(?int $agencyVendorId): void
    {
        if (!$agencyVendorId) {
            return;
        }

        $vendor = AgencyVendor::find($agencyVendorId);
        if (!$vendor) {
            return;
        }

        $totalExpenses = Expense::where('agency_vendor_id', $agencyVendorId)->sum('amount');
        $totalPayments = Payment::where('agency_vendor_id', $agencyVendorId)->sum('amount');

        $vendor->balance = $totalExpenses - $totalPayments;
        $vendor->saveQuietly();
    }


    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $this->syncBalance($payment->agency_vendor_id);
    }

    /**
     * Handle the Payment "updated" event.
     * If the agency_vendor_id or expense_id changed, sync BOTH old and new.
     */
    public function updated(Payment $payment): void
    {
        // Sync old vendor if it changed
        if ($payment->wasChanged('agency_vendor_id')) {
            $oldVendorId = $payment->getOriginal('agency_vendor_id');
            $this->syncBalance($oldVendorId);
        }

        $this->syncBalance($payment->agency_vendor_id);
    }

    /**
     * Handle the Payment "deleted" (soft delete) event.
     */
    public function deleted(Payment $payment): void
    {
        $this->syncBalance($payment->agency_vendor_id);
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        $this->syncBalance($payment->agency_vendor_id);
    }
}
