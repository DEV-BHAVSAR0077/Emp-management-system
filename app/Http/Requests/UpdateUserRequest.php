<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Only authenticated users can submit these forms.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     * Works for both create (POST /users) and update (PUT /users/{user}).
     */
    public function rules(): array
    {
        // On update, $this->route('user') is the User model being updated.
        $userId = optional($this->route('user'))->id;
        
        $rules = [
            'name'  => 'required|string|min:2|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
        ];

        // Password is required only on create; optional on update.
        if ($this->isMethod('POST')) {
            $rules['password']              = ['required', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['required'];
        } else {
            $rules['password']              = ['nullable', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['nullable'];
        }

        // Role field — only validated if sent by an admin
        if (Auth::check() && Auth::user()->hasPermission('edit-role')) {
            // Validate the role ID exists in the roles table
            $rules['role_id'] = ['nullable', 'exists:roles,id'];
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required'                  => 'Full name is required.',
            'email.required'                 => 'Email address is required.',
            'email.email'                    => 'Please enter a valid email address.',
            'email.unique'                   => 'This email address is already taken.',
            'password.required'              => 'Password is required.',
            'password.min'                   => 'Password must be at least 8 characters.',
            'password.confirmed'             => 'Password confirmation doesn\'t match.',
            'password_confirmation.required' => 'Please confirm the password.',
            'role.in'                        => 'Invalid role selected.',
        ];
    }
}
