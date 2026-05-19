<?php

namespace App\Http\Requests;

use App\Models\AgencyVendor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AVStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:150',
            'type'           => ['required', Rule::in(array_keys(AgencyVendor::TYPES))],
            'email'          => 'nullable|email|max:150',
            'phone_number'   => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:150',
        ];
    }
}
