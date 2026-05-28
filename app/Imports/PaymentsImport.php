<?php

namespace App\Imports;

use App\Models\AgencyVendor;
use App\Models\Payment;
use App\Services\SyncBalance;
use App\Services\VendorLedgerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaymentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $vendors = AgencyVendor::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtolower(trim($key)) => $item];
        });

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (!isset($row['amount']) && !isset($row['payment_type']) && !isset($row['agency_vendor'])) {
                    continue;
                }

                $rowNumber = $index + 2;

                // Validate basic fields exist
                if (!isset($row['amount']) || !isset($row['payment_type']) || !isset($row['payment_date']) || !isset($row['agency_vendor'])) {
                    throw ValidationException::withMessages([
                        'file' => ["Row {$rowNumber} -> Missing one or more required columns (amount, payment_type, payment_date, agency_vendor)."]
                    ]);
                }

                // Parse Vendor
                $vendorName = strtolower(trim($row['agency_vendor']));
                if (!isset($vendors[$vendorName])) {
                    throw ValidationException::withMessages([
                        'agency_vendor' => ["Row {$rowNumber} -> Agency/Vendor '{$row['agency_vendor']}' not found in the system. You must create the vendor first."]
                    ]);
                }
                $vendorId = $vendors[$vendorName];

                // Parse amount
                $amount = floatval($row['amount']);
                if ($amount <= 0) {
                    throw ValidationException::withMessages([
                        'amount' => ["Row {$rowNumber} -> Amount must be greater than 0."]
                    ]);
                }

                // Parse type
                $typeStr = strtolower(trim($row['payment_type']));
                $paymentType = null;
                if ($typeStr === 'credit' || $typeStr === '1') {
                    $paymentType = 1;
                } elseif ($typeStr === 'debit' || $typeStr === '0') {
                    $paymentType = 0;
                } else {
                    throw ValidationException::withMessages([
                        'payment_type' => ["Row {$rowNumber} -> Payment type must be either 'Credit' or 'Debit'."]
                    ]);
                }

                // Parse date
                $dateStr = $row['payment_date'];
                $paymentDate = null;
                if (is_numeric($dateStr)) {
                    $paymentDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateStr)->format('Y-m-d');
                } else {
                    $parsed = strtotime($dateStr);
                    if (!$parsed) {
                        throw ValidationException::withMessages([
                            'payment_date' => ["Row {$rowNumber} -> Invalid date format '{$row['payment_date']}'."]
                        ]);
                    }
                    $paymentDate = date('Y-m-d', $parsed);
                }

                // Parse Note
                $note = isset($row['notes']) ? mb_substr(trim($row['notes']), 0, 1000) : null;
                if ($note === '') $note = null;

                // Create Payment
                $payment = Payment::query()->create([
                    'user_id'          => Auth::id(),
                    'agency_vendor_id' => $vendorId,
                    'amount'           => $amount,
                    'payment_type'     => $paymentType,
                    'payment_date'     => $paymentDate,
                    'notes'            => $note,
                ]);

                // Update ledger
                $newBalance = SyncBalance::updateBalance($vendorId, $amount, 'payment', 'add', $paymentType);
                VendorLedgerService::addEntry($payment, $vendorId, $amount, 'payment', $newBalance, 'Payment Added (Import)', $paymentType);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
