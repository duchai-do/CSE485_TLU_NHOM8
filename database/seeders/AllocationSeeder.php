<?php

namespace Database\Seeders;

use App\Models\Allocation;
use App\Models\Bed;
use App\Models\RoomRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllocationSeeder extends Seeder
{
    public function run(): void
    {
        $allocator = User::query()
            ->whereIn('role', ['admin', 'staff'])
            ->where('status', true)
            ->orderBy('id')
            ->first()
            ?? User::query()->orderBy('id')->first();

        $registrations = RoomRegistration::query()
            ->with('student')
            ->whereIn('status', ['pending', 'approved'])
            ->whereDoesntHave('allocation')
            ->orderByDesc('priority_score')
            ->orderBy('id')
            ->get();

        $beds = Bed::query()
            ->with('room')
            ->where('status', 'empty')
            ->orderBy('id')
            ->get();

        if (! $allocator || $registrations->isEmpty() || $beds->isEmpty()) {
            $this->command?->warn(
                'Bỏ qua AllocationSeeder: cần users, room_registrations và beds trống trước.'
            );
            return;
        }

        $selectedRegistration = null;
        $selectedBed = null;

        foreach ($registrations as $registration) {
            foreach ($beds as $bed) {
                if ($this->genderMatches($registration->student?->gender, $bed->room?->type)
                    && $this->roomTypeMatches($registration->preferred_room_type, $bed->room?->capacity)) {
                    $selectedRegistration = $registration;
                    $selectedBed = $bed;
                    break 2;
                }
            }
        }

        if (! $selectedRegistration || ! $selectedBed) {
            $this->command?->warn(
                'Bỏ qua AllocationSeeder: chưa có đăng ký và giường tương thích giới tính/loại phòng.'
            );
            return;
        }

        DB::transaction(function () use ($selectedRegistration, $selectedBed, $allocator): void {
            Allocation::updateOrCreate(
                ['registration_id' => $selectedRegistration->id],
                [
                    'student_id' => $selectedRegistration->student_id,
                    'bed_id' => $selectedBed->id,
                    'allocated_by' => $allocator->id,
                    'start_date' => now()->startOfMonth()->toDateString(),
                    'end_date' => now()->addMonths(5)->endOfMonth()->toDateString(),
                    'status' => 'active',
                    'note' => 'Dữ liệu mẫu của thành viên 3.',
                ]
            );

            $selectedRegistration->update([
                'status' => 'allocated',
                'reviewed_by' => $allocator->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            $selectedBed->update(['status' => 'occupied']);
        });
    }

    private function genderMatches(?string $studentGender, ?string $roomType): bool
    {
        if ($roomType === 'other') {
            return true;
        }

        $normalized = mb_strtolower(trim((string) $studentGender));

        $studentType = match ($normalized) {
            'nam', 'male', 'm' => 'male',
            'nữ', 'nu', 'female', 'f' => 'female',
            default => null,
        };

        return $studentType !== null && $studentType === $roomType;
    }

    private function roomTypeMatches(?string $preferredRoomType, ?int $capacity): bool
    {
        if (! $preferredRoomType || ! $capacity) {
            return true;
        }

        if (! preg_match('/(\d+)/u', $preferredRoomType, $matches)) {
            return true;
        }

        return (int) $matches[1] === (int) $capacity;
    }
}
