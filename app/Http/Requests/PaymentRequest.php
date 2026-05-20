<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'agency_vendor_id' => 'required|exists:agency_vendors,id',
            'amount'           => 'required|numeric|min:0.01|max:9999999999.99',
            'notes'            => 'nullable|string|max:1000',
            'payment_date'     => 'required|date',
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
            'agency_vendor_id.required' => 'Please select an agency or vendor.',
            'agency_vendor_id.exists'   => 'The selected agency/vendor is invalid.',
            'amount.required'           => 'Payment amount is required.',
            'amount.numeric'            => 'Amount must be a valid number.',
            'amount.min'                => 'Amount must be at least 0.01.',
            'payment_date.required'     => 'Payment date is required.',
            'payment_date.date'         => 'Please enter a valid date.',
            'notes.max'                 => 'Notes may not exceed 1000 characters.',
        ];
    }
}
