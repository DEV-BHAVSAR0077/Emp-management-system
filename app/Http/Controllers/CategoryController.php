<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // Show form to create a new category
    public function create()
    {
        return view('categories.create_category');
    }

    // Show form to edit an existing category
    public function edit(Category $category)
    {
        return view('categories.edit_category', compact('category'));
    }

    // Store a new main category along with optional dynamic sub-categories
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'sub_categories' => 'nullable|array',
            'sub_categories.*.name' => 'required|string|max:100|distinct', // distinct prevents duplicate names in the same submission
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category already exists.',
            'sub_categories.*.name.required' => 'Sub-category name cannot be empty.',
            'sub_categories.*.name.distinct' => 'Duplicate sub-category names are not allowed.',
        ]);

        $category = Category::create(['name' => $request->name]);

        if ($request->has('sub_categories')) {
            foreach ($request->sub_categories as $subData) {
                if (!empty($subData['name'])) {
                    $category->subCategories()->create(['name' => $subData['name']]);
                }
            }
        }

        return redirect()->route('dashboard', ['tab' => 'categories'])->with('success', 'Category created successfully.');
    }

    // Update an existing category and sync sub-categories
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category->id)],
            'sub_categories' => 'nullable|array',
            'sub_categories.*.id' => 'nullable|exists:sub_categories,id',
            'sub_categories.*.name' => 'required|string|max:100|distinct',
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category already exists.',
            'sub_categories.*.name.required' => 'Sub-category name cannot be empty.',
            'sub_categories.*.name.distinct' => 'Duplicate sub-category names are not allowed.',
        ]);

        $category->update(['name' => $request->name]);

        // Sync sub-categories dynamically
        if ($request->has('sub_categories')) {
            $submittedIds = [];
            foreach ($request->sub_categories as $subData) {
                if (!empty($subData['id'])) {
                    // Update existing
                    $sub = SubCategory::find($subData['id']);
                    if ($sub && $sub->category_id === $category->id) {
                        $sub->update(['name' => $subData['name']]);
                        $submittedIds[] = $sub->id;
                    }
                } else {
                    // Create new
                    if (!empty($subData['name'])) {
                        $newSub = $category->subCategories()->create(['name' => $subData['name']]);
                        $submittedIds[] = $newSub->id;
                    }
                }
            }
            // Soft delete any sub-categories that were removed from the UI but belonged to this category
            $category->subCategories()->whereNotIn('id', $submittedIds)->delete();
        } else {
            // If no sub-categories submitted, soft delete all existing ones
            $category->subCategories()->delete();
        }

        return redirect()->route('dashboard', ['tab' => 'categories'])->with('success', 'Category updated successfully.');
    }

    // Soft delete a main category and its sub-categories
    public function destroy(Category $category)
    {
        $category->subCategories()->delete();
        $category->delete();

        return redirect()->route('dashboard', ['tab' => 'categories'])->with('success', 'Category deleted successfully.');
    }

    // Soft delete a single sub-category via AJAX
    public function destroySubCategory(SubCategory $subCategory)
    {
        $subCategory->delete();

        return response()->json(['success' => true]);
    }

    // Get sub-categories for a given category (JSON) - Used by Expense Dropdown
    public function getSubCategories(Category $category)
    {
        return response()->json(
            $category->subCategories()->orderBy('name')->get(['id', 'name'])
        );
    }
}
