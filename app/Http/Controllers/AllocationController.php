<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member3\StoreAllocationRequest;
use App\Http\Requests\Member3\UpdateAllocationRequest;
use App\Models\Allocation;
use App\Models\Bed;
use App\Models\Room;
use App\Models\RoomRegistration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AllocationController extends Controller
{
    public function registrations(Request $request): View
    {
        $registrations = RoomRegistration::query()
            ->with(['student.user', 'reviewer', 'allocation.bed.room.building'])
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    (string) $request->input('status')
                )
            )
            ->orderByDesc('priority_score')
            ->orderByDesc('created_at')
            ->get();

        return view(
            'member3.registrations.index',
            compact('registrations')
        );
    }

    public function approveRegistration(
        RoomRegistration $registration
    ): RedirectResponse {
        if ($registration->status !== 'pending') {
            return back()->with(
                'error',
                'Chỉ đơn pending mới được duyệt.'
            );
        }

        $registration->update([
            'status' => 'approved',
            'reviewed_by' => $this->actorId(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with(
            'success',
            'Đã duyệt đơn đăng ký.'
        );
    }

    public function rejectRegistration(
        Request $request,
        RoomRegistration $registration
    ): RedirectResponse {
        if ($registration->status !== 'pending') {
            return back()->with(
                'error',
                'Chỉ đơn pending mới được từ chối.'
            );
        }

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $registration->update([
            'status' => 'rejected',
            'reviewed_by' => $this->actorId(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with(
            'success',
            'Đã từ chối đơn đăng ký.'
        );
    }

    public function index(Request $request): View
    {
        $allocations = Allocation::query()
            ->with([
                'registration',
                'student.user',
                'bed.room.building',
                'allocator',
                'contract',
            ])
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    (string) $request->input('status')
                )
            )
            ->latest('id')
            ->get();

        return view(
            'member3.allocations.index',
            compact('allocations')
        );
    }

    public function create(Request $request): View
    {
        $registrations = RoomRegistration::query()
            ->with('student.user')
            ->where('status', 'approved')
            ->whereDoesntHave('allocation')
            ->orderByDesc('priority_score')
            ->get();

        $selectedRegistrationId = (int) old(
            'registration_id',
            $request->input('registration_id')
        );

        $selectedRegistration = $selectedRegistrationId
            ? $registrations->firstWhere('id', $selectedRegistrationId)
            : null;

        // Nếu chỉ còn đúng 1 đơn đã duyệt thì tự chọn đơn đó.
        if (! $selectedRegistration && $registrations->count() === 1) {
            $selectedRegistration = $registrations->first();
        }

        // Chỉ tải các giường phù hợp với giới tính và sức chứa
        // của đơn đang được chọn.
        $beds = $selectedRegistration
            ? $this->availableBeds(null, $selectedRegistration)
            : collect();

        return view(
            'member3.allocations.create',
            compact(
                'registrations',
                'beds',
                'selectedRegistration'
            )
        );
    }

    public function store(
        StoreAllocationRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $registration = RoomRegistration::query()
                ->lockForUpdate()
                ->findOrFail($data['registration_id']);

            $bed = Bed::query()
                ->with('room')
                ->lockForUpdate()
                ->findOrFail($data['bed_id']);

            // Kiểm tra lại trạng thái ngay trong transaction.
            if ($registration->status !== 'approved') {
                abort(
                    422,
                    'Đơn đăng ký không còn ở trạng thái approved.'
                );
            }

            if ($bed->status !== 'empty') {
                abort(
                    422,
                    'Giường được chọn không còn trống.'
                );
            }

            if ($bed->room?->status === 'maintenance') {
                abort(
                    422,
                    'Phòng của giường này đang bảo trì.'
                );
            }

            Allocation::create([
                'registration_id' => $registration->id,
                'student_id' => $registration->student_id,
                'bed_id' => $bed->id,
                'allocated_by' => $this->actorId(),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'status' => 'active',
                'note' => $data['note'] ?? null,
            ]);

            $registration->update([
                'status' => 'allocated',
                'reviewed_by' => $registration->reviewed_by
                    ?: $this->actorId(),
                'reviewed_at' => $registration->reviewed_at
                    ?: now(),
                'rejection_reason' => null,
            ]);

            $bed->update([
                'status' => 'occupied',
            ]);

            $this->refreshRoomStatus($bed->room_id);
        });

        return redirect()
            ->route('member3.allocations.index')
            ->with(
                'success',
                'Xếp giường cho sinh viên thành công.'
            );
    }

    public function edit(
        Allocation $allocation
    ): View|RedirectResponse {
        $allocation->load([
            'student.user',
            'registration.student',
            'bed.room.building',
            'contract',
        ]);

        if ($allocation->status !== 'active') {
            return redirect()
                ->route('member3.allocations.index')
                ->with(
                    'error',
                    'Chỉ allocation active mới được chỉnh sửa.'
                );
        }

        $beds = $this->availableBeds(
            $allocation->bed_id,
            $allocation->registration
        );

        return view(
            'member3.allocations.edit',
            compact('allocation', 'beds')
        );
    }

    public function update(
        UpdateAllocationRequest $request,
        Allocation $allocation
    ): RedirectResponse {
        $data = $request->validated();

        DB::transaction(
            function () use ($allocation, $data): void {
                $allocation->load([
                    'bed',
                    'student',
                    'registration',
                ]);

                $oldBed = Bed::query()
                    ->lockForUpdate()
                    ->findOrFail($allocation->bed_id);

                $newBed = Bed::query()
                    ->lockForUpdate()
                    ->findOrFail($data['bed_id']);

                $allocation->update([
                    'bed_id' => $newBed->id,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'] ?? null,
                    'note' => $data['note'] ?? null,
                ]);

                if ($oldBed->id !== $newBed->id) {
                    $oldBed->update([
                        'status' => 'empty',
                    ]);

                    $newBed->update([
                        'status' => 'occupied',
                    ]);

                    $this->refreshRoomStatus(
                        $oldBed->room_id
                    );

                    $this->refreshRoomStatus(
                        $newBed->room_id
                    );
                }
            }
        );

        return redirect()
            ->route('member3.allocations.index')
            ->with(
                'success',
                'Cập nhật xếp giường thành công.'
            );
    }

    public function end(
        Allocation $allocation
    ): RedirectResponse {
        if ($allocation->status !== 'active') {
            return back()->with(
                'error',
                'Allocation này không còn hoạt động.'
            );
        }

        if (
            $allocation->contract
            && $allocation->contract->status === 'active'
        ) {
            return back()->with(
                'error',
                'Hãy chấm dứt hợp đồng trước khi kết thúc allocation.'
            );
        }

        DB::transaction(function () use ($allocation): void {
            $allocation->load('bed');

            $allocation->update([
                'status' => 'ended',
                'end_date' => $allocation->end_date
                    ?: now()->toDateString(),
            ]);

            if ($allocation->bed) {
                $allocation->bed->update([
                    'status' => 'empty',
                ]);

                $this->refreshRoomStatus(
                    $allocation->bed->room_id
                );
            }
        });

        return back()->with(
            'success',
            'Đã kết thúc xếp giường và trả giường về trạng thái trống.'
        );
    }

    public function cancel(
        Allocation $allocation
    ): RedirectResponse {
        if ($allocation->status !== 'active') {
            return back()->with(
                'error',
                'Allocation này không còn hoạt động.'
            );
        }

        if ($allocation->contract) {
            return back()->with(
                'error',
                'Không thể hủy allocation đã phát sinh hợp đồng.'
            );
        }

        DB::transaction(function () use ($allocation): void {
            $allocation->load([
                'bed',
                'registration',
            ]);

            $allocation->update([
                'status' => 'cancelled',
                'end_date' => now()->toDateString(),
            ]);

            $allocation->registration?->update([
                'status' => 'approved',
            ]);

            if ($allocation->bed) {
                $allocation->bed->update([
                    'status' => 'empty',
                ]);

                $this->refreshRoomStatus(
                    $allocation->bed->room_id
                );
            }
        });

        return back()->with(
            'success',
            'Đã hủy allocation. Đơn đăng ký trở lại trạng thái approved.'
        );
    }

    private function availableBeds(
        ?int $includeBedId = null,
        ?RoomRegistration $registration = null
    ) {
        if ($registration) {
            $registration->loadMissing('student');
        }

        $studentRoomType = $this->studentRoomType(
            $registration?->student?->gender
        );

        $preferredCapacity = $this->preferredCapacity(
            $registration?->preferred_room_type
        );

        return Bed::query()
            ->with('room.building')
            ->where(
                function ($query) use ($includeBedId): void {
                    $query->where('status', 'empty');

                    if ($includeBedId) {
                        $query->orWhere(
                            'id',
                            $includeBedId
                        );
                    }
                }
            )
            ->whereHas(
                'room',
                function ($query) use (
                    $studentRoomType,
                    $preferredCapacity
                ): void {
                    $query->where(
                        'status',
                        '!=',
                        'maintenance'
                    );

                    if ($studentRoomType) {
                        $query->whereIn(
                            'type',
                            [
                                $studentRoomType,
                                'other',
                            ]
                        );
                    }

                    if ($preferredCapacity) {
                        $query->where(
                            'capacity',
                            $preferredCapacity
                        );
                    }
                }
            )
            ->orderBy('room_id')
            ->orderBy('bed_number')
            ->get();
    }

    private function studentRoomType(
        ?string $gender
    ): ?string {
        $normalized = mb_strtolower(
            trim((string) $gender)
        );

        return match ($normalized) {
            'nam', 'male', 'm' => 'male',
            'nữ', 'nu', 'female', 'f' => 'female',
            default => null,
        };
    }

    private function preferredCapacity(
        ?string $preferredRoomType
    ): ?int {
        if (! $preferredRoomType) {
            return null;
        }

        if (
            ! preg_match(
                '/(\d+)/u',
                $preferredRoomType,
                $matches
            )
        ) {
            return null;
        }

        return (int) $matches[1];
    }

    private function actorId(): int
    {
        $id = Auth::id()
            ?? User::query()
                ->whereIn(
                    'role',
                    ['admin', 'staff']
                )
                ->where('status', true)
                ->orderBy('id')
                ->value('id')
            ?? User::query()
                ->orderBy('id')
                ->value('id');

        abort_if(
            ! $id,
            422,
            'Cần có ít nhất một user để thực hiện thao tác.'
        );

        return (int) $id;
    }

    private function refreshRoomStatus(
        int $roomId
    ): void {
        $room = Room::find($roomId);

        if (
            ! $room
            || $room->status === 'maintenance'
        ) {
            return;
        }

        $hasEmptyBed = $room
            ->beds()
            ->where('status', 'empty')
            ->exists();

        $room->update([
            'status' => $hasEmptyBed
                ? 'available'
                : 'full',
        ]);
    }
}