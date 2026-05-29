<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\AgencyVendor;
use App\Models\Expense;
use App\Services\SyncBalance;
use App\Services\VendorLedgerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExpensesImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     * @throws ValidationException
     */
    public function collection(Collection $rows)
    {
        $categories = Category::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtolower(trim($key)) => $item];
        });

        $subCategories = \App\Models\SubCategory::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtolower(trim($key)) => $item];
        });

        $vendors = AgencyVendor::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtolower(trim($key)) => $item];
        });

        DB::beginTransaction();

        try {
            $validatedExpenses = [];
            foreach ($rows as $index => $row) {
                // Laravel Excel rows are 0-indexed and heading row is not included in the collection, so +2 gives the excel row number
                $rowNumber = $index + 2;

                $validator = Validator::make($row->toArray(), [
                    'expense_name'  => 'required|string|max:150',
                    'amount'        => 'required|numeric|min:1|max:9999999999.99',
                    'date'          => 'required|date',
                    'category'      => 'required|string',
                    'sub_category'  => 'nullable|string',
                    'agency_vendor' => 'required|string',
                    'note'          => 'nullable|string|max:1000',
                ]);

                if ($validator->fails()) {
                    $errors = [];
                    foreach ($validator->errors()->toArray() as $field => $messages) {
                        foreach ($messages as $message) {
                            $errors[] = "Row {$rowNumber} -> {$field} -> {$message}";
                        }
                    }
                    throw ValidationException::withMessages(['import_errors' => $errors]);
                }

                // Resolve Category
                $categoryName = strtolower(trim($row['category']));
                if (!isset($categories[$categoryName])) {
                    $newCategory = Category::create(['name' => trim($row['category'])]);
                    $categories[$categoryName] = $newCategory->id;
                    $categoryId = $newCategory->id;
                } else {
                    $categoryId = $categories[$categoryName];
                }

                // Resolve Sub-Category (Nullable)
                $subCategoryId = null;
                if (!empty($row['sub_category'])) {
                    $subCategoryName = strtolower(trim($row['sub_category']));
                    if (!isset($subCategories[$subCategoryName])) {
                        $newSubCategory = \App\Models\SubCategory::create([
                            'category_id' => $categoryId,
                            'name'        => trim($row['sub_category'])
                        ]);
                        $subCategories[$subCategoryName] = $newSubCategory->id;
                        $subCategoryId = $newSubCategory->id;
                    } else {
                        $subCategoryId = $subCategories[$subCategoryName];
                    }
                }

                // Resolve Vendor (Required)
                $vendorName = strtolower(trim($row['agency_vendor']));
                if (!isset($vendors[$vendorName])) {
                    throw ValidationException::withMessages([
                        'import_errors' => ["Row {$rowNumber} -> agency_vendor -> Agency / Vendor '{$row['agency_vendor']}' not found in the system."]
                    ]);
                }
                $vendorId = $vendors[$vendorName];

                // Parse Date
                try {
                    $expenseDate = Carbon::parse($row['date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    throw ValidationException::withMessages([
                        'import_errors' => ["Row {$rowNumber} -> date -> Invalid date format."]
                    ]);
                }

                $note = trim($row['note']);

                $expenseName = trim($row['expense_name']);
                $expenseAmount = $row['amount'];
                $expenseNote = mb_substr($note, 0, 1000);

                $validatedExpenses[] = [
                    'user_id'                 => Auth::id(),
                    'expense_category_id'     => $categoryId,
                    'expense_sub_category_id' => $subCategoryId,
                    'agency_vendor_id'        => $vendorId,
                    'name'                    => $expenseName,
                    'amount'                  => $expenseAmount,
                    'expense_date'            => $expenseDate,
                    'note'                    => $expenseNote,
                ];
            }

            // Insert in chunks of 10 after all validations are successfully done
            $chunks = array_chunk($validatedExpenses, 10);
            foreach ($chunks as $chunk) {
                foreach ($chunk as $data) {
                    $expense = Expense::query()->create($data);

                    // Update Ledger
                    $newBalance = SyncBalance::updateBalance($expense->agency_vendor_id, $expense->amount, 'expense', 'add');
                    if ($expense->agency_vendor_id) {
                        VendorLedgerService::addEntry($expense, $expense->agency_vendor_id, $expense->amount, 'expense', $newBalance, 'Expense Added (Excel Import)');
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Re-throw ValidationException to be caught by the controller
            if ($e instanceof ValidationException) {
                throw $e;
            }
            throw ValidationException::withMessages([
                'import_errors' => ["An unexpected error occurred during import: " . $e->getMessage()]
            ]);
        }
    }
}
