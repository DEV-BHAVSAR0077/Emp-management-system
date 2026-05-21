<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpanceRequest;
use App\Models\AgencyVendor;
use App\Models\Category;
use App\Models\Expense;
use App\Models\SubCategory;
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
            ->paginate(10, ['*'], 'expense_page');

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
        $agencyVendors = AgencyVendor::orderBy('name')->get();

        return view('expenses.create_expense', [
            'user'          => Auth::user(),
            'categories'    => $categories,
            'agencyVendors' => $agencyVendors,
        ]);
    }

    // Store a newly created expense.
    public function store(ExpanceRequest $request)
    {

        Expense::create([
            'user_id'                  => Auth::id(),
            'expense_category_id'      => $request->expense_category_id,
            'expense_sub_category_id'  => $request->expense_sub_category_id,
            'agency_vendor_id'         => $request->agency_vendor_id ?: null,
            'name'                     => $request->name,
            'amount'                   => $request->amount,
            'expense_date'             => $request->expense_date,
            'note'                     => $request->note,
        ]);

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense created successfully.');
    }

    // Show the form for editing the specified expense.
    public function edit(Expense $expense)
    {
        $authUser      = Auth::user();
        $categories    = Category::with('subCategories')->orderBy('name')->get();
        $agencyVendors = AgencyVendor::orderBy('name')->get();

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

        $expense->update([
            'expense_category_id'      => $request->expense_category_id,
            'expense_sub_category_id'  => $request->expense_sub_category_id,
            'agency_vendor_id'         => $request->agency_vendor_id ?: null,
            'name'                     => $request->name,
            'amount'                   => $request->amount,
            'expense_date'             => $request->expense_date,
            'note'                     => $request->note,
        ]);

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense updated successfully.');
    }

    // Soft-delete the specified expense.
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }

    // Restore a soft-deleted expense.
    public function restore(Expense $expense)
    {
        $expense->restore();

        return back()->with('success', 'Expense restored successfully.');
    }
}
