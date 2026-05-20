<?php

namespace App\Observers;

use App\Models\AgencyVendor;
use App\Models\Expense;

class ExpenseObserver
{
    /**
     * Recalculate and persist the balance for a given agency_vendor_id.
     * Uses only active (non-trashed) expenses for the sum.
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
        $totalPayments = \App\Models\Payment::where('agency_vendor_id', $agencyVendorId)->sum('amount');

        $vendor->balance = $totalExpenses - $totalPayments;
        $vendor->saveQuietly(); // saveQuietly avoids triggering Vendor observers
    }

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->syncBalance($expense->agency_vendor_id);
    }

    /**
     * Handle the Expense "updated" event.
     * If the agency_vendor_id changed, sync BOTH old and new vendors.
     */
    public function updated(Expense $expense): void
    {
        // Sync the old vendor if it changed
        if ($expense->wasChanged('agency_vendor_id')) {
            $oldId = $expense->getOriginal('agency_vendor_id');
            $this->syncBalance($oldId);
        }

        $this->syncBalance($expense->agency_vendor_id);
    }

    /**
     * Handle the Expense "deleted" (soft delete) event.
     */
    public function deleted(Expense $expense): void
    {
        $this->syncBalance($expense->agency_vendor_id);
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        $this->syncBalance($expense->agency_vendor_id);
    }
}
