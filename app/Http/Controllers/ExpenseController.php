<?php

namespace App\Http\Controllers;

use App\Models\AgencyVendor;
use App\Models\Category;
use App\Models\Expense;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    // Shared validation rules for store and update
    private function validationRules(Request $request): array
    {
        return [
            'name'                     => 'required|string|max:150',
            'amount'                   => 'required|numeric|min:0.01|max:9999999999.99',
            'expense_category_id'      => 'required|exists:categories,id',
            'expense_sub_category_id'  => [
                'nullable',
                'exists:sub_categories,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $sub = SubCategory::find($value);
                        if (!$sub || $sub->category_id != $request->expense_category_id) {
                            $fail('The selected sub-category does not belong to the chosen category.');
                        }
                    }
                },
            ],
            'expense_date'             => 'required|date',
            'note'                     => 'nullable|string|max:1000',
            'agency_vendor_id'         => 'nullable|exists:agency_vendors,id',
        ];
    }

    // Custom validation messages
    private function validationMessages(): array
    {
        return [
            'name.required'                    => 'Expense name is required.',
            'name.max'                         => 'Expense name may not exceed 150 characters.',
            'amount.required'                  => 'Amount is required.',
            'amount.numeric'                   => 'Amount must be a valid number.',
            'amount.min'                       => 'Amount must be at least 0.01.',
            'expense_category_id.required'     => 'Please select a category.',
            'expense_category_id.exists'       => 'The selected category is invalid.',
            'expense_sub_category_id.exists'   => 'The selected sub-category is invalid.',
            'expense_date.required'            => 'Expense date is required.',
            'expense_date.date'                => 'Please enter a valid date.',
            'note.max'                         => 'Note may not exceed 1000 characters.',
            'agency_vendor_id.exists'          => 'The selected agency/vendor is invalid.',
        ];
    }

    // Redirect to dashboard with expenses tab active.
    public function index()
    {
        return redirect()->route('dashboard', ['tab' => 'expenses']);
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
    public function store(Request $request)
    {
        $request->validate($this->validationRules($request), $this->validationMessages());

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

        return redirect()->route('dashboard', ['tab' => 'expenses'])
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
    public function update(Request $request, Expense $expense)
    {
        $request->validate($this->validationRules($request), $this->validationMessages());

        $expense->update([
            'expense_category_id'      => $request->expense_category_id,
            'expense_sub_category_id'  => $request->expense_sub_category_id,
            'agency_vendor_id'         => $request->agency_vendor_id ?: null,
            'name'                     => $request->name,
            'amount'                   => $request->amount,
            'expense_date'             => $request->expense_date,
            'note'                     => $request->note,
        ]);

        return redirect()->route('dashboard', ['tab' => 'expenses'])
                         ->with('success', 'Expense updated successfully.');
    }

    // Soft-delete the specified expense.
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }

    // Restore a soft-deleted expense.
    public function restore($id)
    {
        $expense = Expense::onlyTrashed()->findOrFail($id);

        $expense->restore();

        return back()->with('success', 'Expense restored successfully.');
    }
}
