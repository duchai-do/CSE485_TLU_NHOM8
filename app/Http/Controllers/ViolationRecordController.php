<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member3\StoreViolationRecordRequest;
use App\Models\Contract;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ViolationRecordController extends Controller
{
    public function index(): View
    {
        $violations = ViolationRecord::query()
            ->with(['student.user', 'contract', 'recorder'])
            ->latest('violation_date')
            ->latest('id')
            ->get();

        return view('member3.violations.index', compact('violations'));
    }

    public function create(): View
    {
        $students = Student::with('user')->orderBy('student_code')->get();
        $contracts = Contract::with('allocation.student.user')->latest('id')->get();

        return view('member3.violations.create', compact('students', 'contracts'));
    }

    public function store(StoreViolationRecordRequest $request): RedirectResponse
    {
        ViolationRecord::create([
            ...$request->validated(),
            'recorded_by' => $this->actorId(),
            'status' => 'pending',
            'resolved_at' => null,
        ]);

        return redirect()
            ->route('member3.violations.index')
            ->with('success', 'Ghi nhận vi phạm thành công.');
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
}
