<?php

namespace App\Http\Requests\Member3;

use App\Models\Allocation;
use App\Models\Bed;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Allocation|null $allocation */
            $allocation = $this->route('allocation');
            $bed = Bed::with('room')->find($this->integer('bed_id'));

            if (! $allocation || ! $bed) {
                return;
            }

            if ($allocation->status !== 'active') {
                $validator->errors()->add('bed_id', 'Chỉ allocation active mới được chỉnh sửa.');
                return;
            }

            if ((int) $allocation->bed_id !== (int) $bed->id && $bed->status !== 'empty') {
                $validator->errors()->add('bed_id', 'Giường mới không còn trống.');
            }

            if ($bed->room?->status === 'maintenance') {
                $validator->errors()->add('bed_id', 'Phòng của giường mới đang bảo trì.');
            }

            $bedAlreadyActive = Allocation::query()
                ->where('bed_id', $bed->id)
                ->where('status', 'active')
                ->where('id', '!=', $allocation->id)
                ->exists();

            if ($bedAlreadyActive) {
                $validator->errors()->add('bed_id', 'Giường này đã có sinh viên đang sử dụng.');
            }

            if (! $this->genderMatches($allocation->student?->gender, $bed->room?->type)) {
                $validator->errors()->add('bed_id', 'Giới tính sinh viên không phù hợp với loại phòng.');
            }

            if (! $this->roomTypeMatches($allocation->registration?->preferred_room_type, $bed->room?->capacity)) {
                $validator->errors()->add('bed_id', 'Sức chứa phòng không đúng loại phòng đã đăng ký.');
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
