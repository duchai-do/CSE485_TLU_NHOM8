<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member3\StoreContractRequest;
use App\Http\Requests\Member3\UpdateContractRequest;
use App\Models\Allocation;
use App\Models\Contract;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        $contracts = Contract::query()
            ->with(['allocation.student.user', 'allocation.bed.room.building'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->input('status')))
            ->latest('id')
            ->get();

        return view('member3.contracts.index', compact('contracts'));
    }

    public function create(): View
    {
        $allocations = Allocation::query()
            ->with(['student.user', 'bed.room.building'])
            ->where('status', 'active')
            ->whereDoesntHave('contract')
            ->latest('id')
            ->get();

        return view('member3.contracts.create', compact('allocations'));
    }

    public function store(StoreContractRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Contract::create([
            ...$data,
            'status' => 'active',
            'signed_at' => now(),
            'terminated_at' => null,
            'termination_reason' => null,
        ]);

        return redirect()
            ->route('member3.contracts.index')
            ->with('success', 'Lập hợp đồng thành công.');
    }

    public function edit(Contract $contract): View|RedirectResponse
    {
        $contract->load(['allocation.student.user', 'allocation.bed.room.building']);

        if ($contract->status === 'terminated') {
            return redirect()
                ->route('member3.contracts.index')
                ->with('error', 'Hợp đồng đã chấm dứt, không thể chỉnh sửa.');
        }

        return view('member3.contracts.edit', compact('contract'));
    }

    public function update(UpdateContractRequest $request, Contract $contract): RedirectResponse
    {
        if ($contract->status === 'terminated') {
            return back()->with('error', 'Hợp đồng đã chấm dứt, không thể chỉnh sửa.');
        }

        $contract->update($request->validated());

        return redirect()
            ->route('member3.contracts.index')
            ->with('success', 'Cập nhật hợp đồng thành công.');
    }

    public function extend(Request $request, Contract $contract): RedirectResponse
    {
        if ($contract->status !== 'active') {
            return back()->with('error', 'Chỉ hợp đồng active mới được gia hạn.');
        }

        $validated = $request->validate([
            'new_end_date' => ['required', 'date', 'after:' . $contract->end_date->toDateString()],
        ], [
            'new_end_date.after' => 'Ngày gia hạn mới phải sau ngày kết thúc hiện tại.',
        ]);

        DB::transaction(function () use ($contract, $validated): void {
            $contract->update(['end_date' => $validated['new_end_date']]);
            $contract->allocation?->update(['end_date' => $validated['new_end_date']]);
        });

        return back()->with('success', 'Gia hạn hợp đồng thành công.');
    }

    public function terminate(Request $request, Contract $contract): RedirectResponse
    {
        if ($contract->status !== 'active') {
            return back()->with('error', 'Hợp đồng này không còn active.');
        }

        $validated = $request->validate([
            'termination_reason' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($contract, $validated): void {
            $contract->load('allocation.bed');

            $contract->update([
                'status' => 'terminated',
                'terminated_at' => now(),
                'termination_reason' => $validated['termination_reason'],
            ]);

            if ($contract->allocation) {
                $contract->allocation->update([
                    'status' => 'ended',
                    'end_date' => now()->toDateString(),
                ]);
            }

            $bed = $contract->allocation?->bed;

            if ($bed) {
                $bed->update(['status' => 'empty']);

                $room = Room::find($bed->room_id);
                if ($room && $room->status !== 'maintenance') {
                    $room->update(['status' => 'available']);
                }
            }
        });

        return back()->with('success', 'Đã chấm dứt hợp đồng và trả giường về trạng thái trống.');
    }
}
