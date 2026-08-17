<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member3\StoreAllocationRequest;
use App\Models\Allocation;
use App\Models\Bed;
use App\Models\RoomRegistration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AllocationController extends Controller
{
    public function registrations(): View
    {
        $registrations = RoomRegistration::query()
            ->with(['student.user', 'reviewer', 'allocation.bed.room.building'])
            ->orderByDesc('priority_score')
            ->orderByDesc('created_at')
            ->get();

        return view('member3.registrations.index', compact('registrations'));
    }

    public function approveRegistration(RoomRegistration $registration): RedirectResponse
    {
        if ($registration->status !== 'pending') {
            return back()->with('error', 'Chỉ đơn pending mới được duyệt.');
        }

        $registration->update([
            'status' => 'approved',
            'reviewed_by' => $this->actorId(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Đã duyệt đơn đăng ký.');
    }

    public function rejectRegistration(Request $request, RoomRegistration $registration): RedirectResponse
    {
        if ($registration->status !== 'pending') {
            return back()->with('error', 'Chỉ đơn pending mới được từ chối.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $registration->update([
            'status' => 'rejected',
            'reviewed_by' => $this->actorId(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Đã từ chối đơn đăng ký.');
    }

    public function index(): View
    {
        $allocations = Allocation::query()
            ->with([
                'registration',
                'student.user',
                'bed.room.building',
                'allocator',
                'contract',
            ])
            ->latest('id')
            ->get();

        return view('member3.allocations.index', compact('allocations'));
    }

    public function create(): View
    {
        $registrations = RoomRegistration::query()
            ->with('student.user')
            ->where('status', 'approved')
            ->whereDoesntHave('allocation')
            ->orderByDesc('priority_score')
            ->get();

        $beds = Bed::query()
            ->with('room.building')
            ->where('status', 'empty')
            ->whereHas('room', fn ($query) => $query->where('status', '!=', 'maintenance'))
            ->orderBy('room_id')
            ->orderBy('bed_number')
            ->get();

        return view('member3.allocations.create', compact('registrations', 'beds'));
    }

    public function store(StoreAllocationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $registration = RoomRegistration::query()
                ->lockForUpdate()
                ->findOrFail($data['registration_id']);

            $bed = Bed::query()
                ->lockForUpdate()
                ->findOrFail($data['bed_id']);

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
                'reviewed_by' => $registration->reviewed_by ?: $this->actorId(),
                'reviewed_at' => $registration->reviewed_at ?: now(),
                'rejection_reason' => null,
            ]);

            $bed->update(['status' => 'occupied']);
            $this->refreshRoomStatus($bed->room_id);
        });

        return redirect()
            ->route('member3.allocations.index')
            ->with('success', 'Xếp giường cho sinh viên thành công.');
    }

    private function actorId(): int
    {
        $id = Auth::id()
            ?? User::query()
                ->whereIn('role', ['admin', 'staff'])
                ->where('status', true)
                ->orderBy('id')
                ->value('id')
            ?? User::query()->orderBy('id')->value('id');

        abort_if(! $id, 422, 'Cần có ít nhất một user để thực hiện thao tác.');

        return (int) $id;
    }

    private function refreshRoomStatus(int $roomId): void
    {
        $room = \App\Models\Room::find($roomId);

        if (! $room || $room->status === 'maintenance') {
            return;
        }

        $hasEmptyBed = $room->beds()->where('status', 'empty')->exists();
        $room->update(['status' => $hasEmptyBed ? 'available' : 'full']);
    }
}
