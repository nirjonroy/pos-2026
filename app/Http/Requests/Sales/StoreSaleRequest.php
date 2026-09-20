<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'payments' => ['nullable', 'array'],
            'payments.*.payment_method' => ['required_with:payments.*.amount', 'in:cash,card,bkash,nagad,bank'],
            'payments.*.amount' => ['required_with:payments.*.payment_method', 'numeric', 'min:0'],
        ];
    }
}
