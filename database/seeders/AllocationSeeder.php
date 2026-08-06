<?php

namespace Database\Seeders;

use App\Models\Allocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllocationSeeder extends Seeder
{
    public function run(): void
    {
        $registration = DB::table('room_registrations')->orderBy('id')->first();
        $bed = DB::table('beds')->where('status', 'available')->orderBy('id')->first()
            ?? DB::table('beds')->orderBy('id')->first();
        $allocator = DB::table('users')->orderBy('id')->first();

        if (! $registration || ! $bed || ! $allocator) {
            $this->command?->warn(
                'Bỏ qua AllocationSeeder: cần có dữ liệu room_registrations, beds và users trước.'
            );
            return;
        }

        DB::transaction(function () use ($registration, $bed, $allocator): void {
            Allocation::updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'student_id' => $registration->student_id,
                    'bed_id' => $bed->id,
                    'allocated_by' => $allocator->id,
                    'start_date' => now()->startOfMonth()->toDateString(),
                    'end_date' => now()->addMonths(5)->endOfMonth()->toDateString(),
                    'status' => 'active',
                    'note' => 'Dữ liệu mẫu của thành viên 3.',
                ]
            );

            DB::table('room_registrations')
                ->where('id', $registration->id)
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => $allocator->id,
                    'reviewed_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('beds')
                ->where('id', $bed->id)
                ->update([
                    'status' => 'occupied',
                    'updated_at' => now(),
                ]);
        });
    }
}
