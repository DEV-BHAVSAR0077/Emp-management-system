<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExpanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                     => 'required|string|max:150',
            'amount'                   => 'required|numeric|min:0.01|max:9999999999.99',
            'expense_category_id'      => 'required|exists:categories,id',
            'expense_sub_category_id'  => [
                'nullable',
                'exists:sub_categories,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $sub = \App\Models\SubCategory::find($value);
                        if (!$sub || $sub->category_id != $this->expense_category_id) {
                            $fail('The selected sub-category does not belong to the chosen category.');
                        }
                    }
                },
            ],
            'expense_date'             => 'required|date',
            'note'                     => 'nullable|string|max:1000',
            'agency_vendor_id'         => 'required|exists:agency_vendors,id',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
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
            'agency_vendor_id.required'        => 'Please select an agency or vendor.',
            'agency_vendor_id.exists'          => 'The selected agency/vendor is invalid.',
        ];
    }
}
