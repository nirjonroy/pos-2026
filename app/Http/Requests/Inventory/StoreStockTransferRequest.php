<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_branch_id' => ['required', 'exists:branches,id'],
            'from_warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'to_branch_id' => ['required', 'exists:branches,id'],
            'to_warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'transfer_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $sameBranch = (string) $this->input('from_branch_id') === (string) $this->input('to_branch_id');
            $sameWarehouse = (string) $this->input('from_warehouse_id') === (string) $this->input('to_warehouse_id');

            if ($sameBranch && $sameWarehouse) {
                $validator->errors()->add('to_branch_id', 'Source and destination cannot be the same.');
            }
        });
    }
}
