<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    // Get sub-categories for a given category (JSON)
    public function subCategories(ExpenseCategory $category)
    {
        return response()->json(
            $category->subCategories()->orderBy('name')->get(['id', 'name'])
        );
    }

    // Store a new category (JSON)
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name',
        ], [
            'name.unique' => 'This category already exists.',
        ]);

        $category = ExpenseCategory::create(['name' => $request->name]);

        return response()->json(['id' => $category->id, 'name' => $category->name], 201);
    }

    // Delete a category (JSON)
    public function destroyCategory(ExpenseCategory $category)
    {
        if ($category->expenses()->exists()) {
            return response()->json(['error' => 'Cannot delete — this category has expenses linked to it.'], 422);
        }

        $category->subCategories()->delete();
        $category->delete();

        return response()->json(['success' => true]);
    }

    // Store a new sub-category (JSON)
    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:100',
            'expense_category_id' => 'required|exists:expense_categories,id',
        ]);

        $exists = ExpenseSubCategory::where('expense_category_id', $request->expense_category_id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['errors' => ['name' => ['This sub-category already exists under the selected category.']]], 422);
        }

        $sub = ExpenseSubCategory::create([
            'expense_category_id' => $request->expense_category_id,
            'name'                => $request->name,
        ]);

        return response()->json(['id' => $sub->id, 'name' => $sub->name], 201);
    }

    // Delete a sub-category (JSON)
    public function destroySubCategory(ExpenseSubCategory $subCategory)
    {
        $subCategory->delete();

        return response()->json(['success' => true]);
    }
}
