<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ViolationRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViolationRecordSeeder extends Seeder
{
    public function run(): void
    {
        $contract = Contract::query()->with('allocation')->orderBy('id')->first();
        $recorder = DB::table('users')->orderBy('id')->first();

        if (! $contract || ! $contract->allocation || ! $recorder) {
            $this->command?->warn(
                'Bỏ qua ViolationRecordSeeder: cần có contract, allocation và users trước.'
            );
            return;
        }

        ViolationRecord::updateOrCreate(
            [
                'student_id' => $contract->allocation->student_id,
                'contract_id' => $contract->id,
                'violation_date' => now()->toDateString(),
                'violation_type' => 'Nội quy ký túc xá',
            ],
            [
                'recorded_by' => $recorder->id,
                'description' => 'Dữ liệu vi phạm mẫu phục vụ kiểm tra migration và relationship.',
                'penalty_amount' => 100000,
                'status' => 'pending',
                'resolved_at' => null,
            ]
        );
    }
}
