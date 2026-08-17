<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member3\StoreContractRequest;
use App\Models\Allocation;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(): View
    {
        $contracts = Contract::query()
            ->with(['allocation.student.user', 'allocation.bed.room.building'])
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
}
