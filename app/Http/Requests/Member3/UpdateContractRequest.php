<?php

namespace App\Http\Requests\Member3;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contract = $this->route('contract');

        return [
            'contract_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('contracts', 'contract_code')->ignore($contract?->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
