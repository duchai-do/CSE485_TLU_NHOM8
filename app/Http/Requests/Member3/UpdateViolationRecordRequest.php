<?php

namespace App\Http\Requests\Member3;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;

class UpdateViolationRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'contract_id' => ['nullable', 'integer', 'exists:contracts,id'],
            'violation_date' => ['required', 'date'],
            'violation_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:5000'],
            'penalty_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('contract_id')) {
                return;
            }

            $contract = Contract::with('allocation')->find($this->integer('contract_id'));

            if ($contract && (int) $contract->allocation?->student_id !== $this->integer('student_id')) {
                $validator->errors()->add(
                    'contract_id',
                    'Hợp đồng được chọn không thuộc sinh viên này.'
                );
            }
        });
    }
}
