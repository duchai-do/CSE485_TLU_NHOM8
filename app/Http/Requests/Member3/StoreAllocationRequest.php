<?php

namespace App\Http\Requests\Member3;

use App\Models\Allocation;
use App\Models\Bed;
use App\Models\RoomRegistration;
use Illuminate\Foundation\Http\FormRequest;

class StoreAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_id' => ['required', 'integer', 'exists:room_registrations,id', 'unique:allocations,registration_id'],
            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'registration_id.unique' => 'Đơn đăng ký này đã được xếp giường.',
            'bed_id.exists' => 'Giường được chọn không tồn tại.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải từ ngày bắt đầu trở đi.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $registration = RoomRegistration::with('student')->find($this->integer('registration_id'));
            $bed = Bed::with('room')->find($this->integer('bed_id'));

            if (! $registration || ! $bed) {
                return;
            }

            if ($registration->status !== 'approved') {
                $validator->errors()->add(
                    'registration_id',
                    'Chỉ được xếp giường cho đơn đã được duyệt (approved).'
                );
            }

            if ($bed->status !== 'empty') {
                $validator->errors()->add('bed_id', 'Giường này hiện không còn trống.');
            }

            if ($bed->room?->status === 'maintenance') {
                $validator->errors()->add('bed_id', 'Phòng của giường này đang bảo trì.');
            }

            $studentAlreadyActive = Allocation::query()
                ->where('student_id', $registration->student_id)
                ->where('status', 'active')
                ->exists();

            if ($studentAlreadyActive) {
                $validator->errors()->add(
                    'registration_id',
                    'Sinh viên này đã có một giường đang sử dụng.'
                );
            }

            $bedAlreadyActive = Allocation::query()
                ->where('bed_id', $bed->id)
                ->where('status', 'active')
                ->exists();

            if ($bedAlreadyActive) {
                $validator->errors()->add('bed_id', 'Giường này đã có allocation đang hoạt động.');
            }

            if (! $this->genderMatches($registration->student?->gender, $bed->room?->type)) {
                $validator->errors()->add('bed_id', 'Giới tính sinh viên không phù hợp với loại phòng.');
            }

            if (! $this->roomTypeMatches($registration->preferred_room_type, $bed->room?->capacity)) {
                $validator->errors()->add('bed_id', 'Sức chứa phòng không đúng loại phòng sinh viên đăng ký.');
            }
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
