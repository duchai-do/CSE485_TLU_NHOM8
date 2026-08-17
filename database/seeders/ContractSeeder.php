<?php

namespace Database\Seeders;

use App\Models\Allocation;
use App\Models\Contract;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $allocation = Allocation::query()
            ->with('bed.room')
            ->where('status', 'active')
            ->orderBy('id')
            ->first();

        if (! $allocation) {
            $this->command?->warn('Bỏ qua ContractSeeder: chưa có allocation active.');
            return;
        }

        Contract::updateOrCreate(
            ['allocation_id' => $allocation->id],
            [
                'contract_code' => 'HD-' . str_pad((string) $allocation->id, 6, '0', STR_PAD_LEFT),
                'start_date' => $allocation->start_date,
                'end_date' => $allocation->end_date ?? now()->addMonths(5)->endOfMonth(),
                'monthly_price' => $allocation->bed?->room?->price ?? 800000,
                'deposit_amount' => 500000,
                'status' => 'active',
                'signed_at' => now(),
                'terminated_at' => null,
                'termination_reason' => null,
            ]
        );
    }
}
