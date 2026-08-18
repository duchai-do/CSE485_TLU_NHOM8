<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member3\StoreViolationRecordRequest;
use App\Http\Requests\Member3\UpdateViolationRecordRequest;
use App\Models\Contract;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ViolationRecordController extends Controller
{
    public function index(Request $request): View
    {
        $violations = ViolationRecord::query()
            ->with(['student.user', 'contract', 'recorder'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->input('status')))
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

    public function edit(ViolationRecord $violation): View|RedirectResponse
    {
        if ($violation->status === 'resolved') {
            return redirect()
                ->route('member3.violations.index')
                ->with('error', 'Biên bản đã xử lý xong, không thể chỉnh sửa.');
        }

        $students = Student::with('user')->orderBy('student_code')->get();
        $contracts = Contract::with('allocation.student.user')->latest('id')->get();

        return view('member3.violations.edit', compact('violation', 'students', 'contracts'));
    }

    public function update(UpdateViolationRecordRequest $request, ViolationRecord $violation): RedirectResponse
    {
        if ($violation->status === 'resolved') {
            return back()->with('error', 'Biên bản đã xử lý xong, không thể chỉnh sửa.');
        }

        $violation->update($request->validated());

        return redirect()
            ->route('member3.violations.index')
            ->with('success', 'Cập nhật biên bản vi phạm thành công.');
    }

    public function resolve(ViolationRecord $violation): RedirectResponse
    {
        if ($violation->status === 'resolved') {
            return back()->with('error', 'Biên bản này đã được xử lý.');
        }

        $violation->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Đã đánh dấu biên bản là đã xử lý.');
    }

    public function destroy(ViolationRecord $violation): RedirectResponse
    {
        if ($violation->status !== 'pending') {
            return back()->with('error', 'Chỉ được xóa biên bản đang pending.');
        }

        $violation->delete();

        return back()->with('success', 'Đã xóa biên bản pending.');
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
