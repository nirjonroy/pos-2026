<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_group_id' => ['nullable', 'exists:customer_groups,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
            'opening_due' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }
}
