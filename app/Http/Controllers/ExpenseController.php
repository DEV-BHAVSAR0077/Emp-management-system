<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpanceRequest;
use App\Models\AgencyVendor;
use App\Models\Category;
use App\Models\Expense;

use App\Services\SyncBalance;
use App\Services\VendorLedgerService;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Validation\ValidationException;
use App\Imports\ExpensesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{


    // Display the expenses list.
    public function index(Request $request)
    {
        $expenseSearch = $request->input('expense_search', '');
        $expenseStatus = $request->input('expense_status', 'active');

        $expenses = Expense::with(['category', 'subCategory', 'user', 'agencyVendor'])
            ->when($expenseStatus === 'trashed', function ($query) {
                $query->onlyTrashed();
            })
            ->when($expenseSearch, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('expense_date', 'desc')
            ->paginate(8, ['*'], 'expense_page');

        return view('expenses.index', [
            'user'          => Auth::user(),
            'expenses'      => $expenses,
            'expenseSearch' => $expenseSearch,
            'expenseStatus' => $expenseStatus,
        ]);
    }

    // Show the form for creating a new expense.
    public function create()
    {
        $categories    = Category::with('subCategories')->orderBy('name')->get();
        $agencyVendors = AgencyVendor::query()->orderBy('name', 'asc')->get();

        return view('expenses.create_expense', [
            'user'          => Auth::user(),
            'categories'    => $categories,
            'agencyVendors' => $agencyVendors,
        ]);
    }

    public function store(ExpanceRequest $request)
    {
        $expenseName = trim($request->name);
        $expenseNote = $request->note ? mb_substr(trim($request->note), 0, 1000) : null;

        $expense = Expense::query()->create([
            'user_id'                  => Auth::id(),
            'expense_category_id'      => $request->expense_category_id,
            'expense_sub_category_id'  => $request->expense_sub_category_id,
            'agency_vendor_id'         => $request->agency_vendor_id ?: null,
            'name'                     => $expenseName,
            'amount'                   => $request->amount,
            'expense_date'             => $request->expense_date,
            'note'                     => $expenseNote,
        ]);

        $newBalance = SyncBalance::updateBalance($expense->agency_vendor_id, $expense->amount, 'expense', 'add');
        if ($expense->agency_vendor_id) {
            VendorLedgerService::addEntry($expense, $expense->agency_vendor_id, $expense->amount, 'expense', $newBalance, 'Expense Added');
        }

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense created successfully.');
    }

    // Show the form for editing the specified expense.
    public function edit(Expense $expense)
    {
        $authUser      = Auth::user();
        $categories    = Category::query()->with('subCategories')->orderBy('name', 'asc')->get();
        $agencyVendors = AgencyVendor::query()->orderBy('name', 'asc')->get();

        return view('expenses.edit_expense', [
            'user'          => $authUser,
            'expense'       => $expense,
            'categories'    => $categories,
            'agencyVendors' => $agencyVendors,
        ]);
    }

    // Update the specified expense.
    public function update(ExpanceRequest $request, Expense $expense)
    {
        $oldVendorId = $expense->agency_vendor_id;
        $oldAmount = $expense->amount;

        $newVendorId = $request->agency_vendor_id ?: null;
        $newAmount = $request->amount;

        $oldBalance = SyncBalance::updateBalance($oldVendorId, $oldAmount, 'expense', 'remove');
        if ($oldVendorId && $oldVendorId != $newVendorId) {
            VendorLedgerService::addRemoveEntry($expense, $oldVendorId, $oldAmount, 'expense', $oldBalance, 'Expense Deleted (Vendor Changed)');
        }

        $expense->fill([
            'expense_category_id'      => $request->expense_category_id,
            'expense_sub_category_id'  => $request->expense_sub_category_id,
            'agency_vendor_id'         => $newVendorId,
            'name'                     => $request->name,
            'amount'                   => $newAmount,
            'expense_date'             => $request->expense_date,
            'note'                     => $request->note,
        ]);
        $expense->save();

        $newBalance = SyncBalance::updateBalance($newVendorId, $newAmount, 'expense', 'add');
        if ($newVendorId) {
            if ($oldVendorId == $newVendorId) {
                VendorLedgerService::addUpdateEntry(
                    $expense, 
                    $newVendorId, 
                    $oldAmount, 'expense', null, 
                    $newAmount, 'expense', null, 
                    $newBalance, 'Expense Updated'
                );
            } else {
                VendorLedgerService::addEntry($expense, $newVendorId, $newAmount, 'expense', $newBalance, 'Expense Added (Vendor Changed)');
            }
        }

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense updated successfully.');
    }

    // Soft-delete the specified expense.
    public function destroy(Expense $expense)
    {
        $newBalance = SyncBalance::updateBalance($expense->agency_vendor_id, $expense->amount, 'expense', 'remove');
        if ($expense->agency_vendor_id) {
            VendorLedgerService::addRemoveEntry($expense, $expense->agency_vendor_id, $expense->amount, 'expense', $newBalance, 'Expense Deleted');
        }
        Expense::destroy($expense->id);

        return back()->with('success', 'Expense deleted successfully.');
    }

    // Restore a soft-deleted expense.
    public function restore(Expense $expense)
    {
        $expense->restore();
        $newBalance = SyncBalance::updateBalance($expense->agency_vendor_id, $expense->amount, 'expense', 'add');
        if ($expense->agency_vendor_id) {
            VendorLedgerService::addEntry($expense, $expense->agency_vendor_id, $expense->amount, 'expense', $newBalance, 'Expense Restored');
        }

        return back()->with('success', 'Expense restored successfully.');
    }

    // ---------------------------------------------------------
    // Excel Import & Template
    // ---------------------------------------------------------

    public function downloadTemplate()
    {
        return Excel::download(new class implements FromArray, WithHeadings {
            public function headings(): array {
                return [
                    'expense_name',
                    'amount',
                    'date',
                    'category',
                    'sub_category',
                    'agency_vendor',
                    'note'
                ];
            }
            public function array(): array {
                return [[
                    'Office Supplies',
                    '150.50',
                    date('Y-m-d'),
                    'Office Expenses',
                    'Stationery',
                    'Amazon',
                    'Bought some pens and paper'
                ]];
            }
        }, 'expense_template.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            Excel::import(
                new ExpensesImport,
                $request->file('excel_file')
            );

            return back()->with('success', 'Expenses imported successfully!');
        } catch (ValidationException $e) {
            $messages = [];
            foreach ($e->errors() as $field => $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $messages[] = $error;
                }
            }
            return back()->withErrors(['import_errors' => $messages]);
        } catch (\Exception $e) {
            return back()->withErrors(['import_errors' => [$e->getMessage()]]);
        }
    }
}
