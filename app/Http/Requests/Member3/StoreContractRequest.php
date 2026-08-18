<?php

namespace App\Http\Requests\Member3;

use App\Models\Allocation;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'allocation_id' => ['required', 'integer', 'exists:allocations,id', 'unique:contracts,allocation_id'],
            'contract_code' => ['required', 'string', 'max:50', 'unique:contracts,contract_code'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $allocation = Allocation::find($this->integer('allocation_id'));

            if ($allocation && $allocation->status !== 'active') {
                $validator->errors()->add(
                    'allocation_id',
                    'Chỉ được lập hợp đồng cho allocation đang hoạt động.'
                );
            }
        });
    }
}
