<?php

namespace App\Http\Controllers;

use App\Models\RoomRegistration;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = RoomRegistration::with(['student.user', 'reviewer']);

        if ($user->role === 'student') {
            $query->where('student_id', $user->student?->id ?? 0);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('student', function ($studentQuery) use ($search) {
                $studentQuery->where('student_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $registrations = $query->latest()->paginate(10)->withQueryString();

        $yearQuery = RoomRegistration::query();
        if ($user->role === 'student') {
            $yearQuery->where('student_id', $user->student?->id ?? 0);
        }

        $academicYears = $yearQuery
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        return view('room-registrations.index', compact('registrations', 'academicYears'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'student') {
            if (!$user->student) {
                return redirect()->route('dashboard')
                    ->with('error', 'Tài khoản chưa có hồ sơ sinh viên.');
            }

            $students = collect([$user->student->load('user')]);
        } else {
            $students = Student::with('user')->orderBy('student_code')->get();
        }

        return view('room-registrations.create', compact('students'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $isStudent = $user->role === 'student';

        $validated = $request->validate([
            'student_id' => $isStudent ? ['nullable'] : ['required', 'exists:students,id'],
            'semester' => ['required', Rule::in(['1', '2', 'Hè'])],
            'academic_year' => ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'preferred_room_type' => ['nullable', Rule::in(['Phòng 4 người', 'Phòng 6 người', 'Phòng 8 người'])],
            'note' => ['nullable', 'string', 'max:2000'],
        ], [
            'academic_year.regex' => 'Năm học phải có dạng 2026-2027.',
        ]);

        $studentId = $isStudent ? $user->student?->id : $validated['student_id'];

        if (!$studentId) {
            return back()->withInput()->withErrors([
                'student_id' => 'Tài khoản chưa có hồ sơ sinh viên.',
            ]);
        }

        $exists = RoomRegistration::query()
            ->where('student_id', $studentId)
            ->where('semester', $validated['semester'])
            ->where('academic_year', $validated['academic_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'student_id' => 'Sinh viên đã đăng ký trong học kỳ và năm học này.',
            ]);
        }

        RoomRegistration::create([
            'student_id' => $studentId,
            'semester' => $validated['semester'],
            'academic_year' => $validated['academic_year'],
            'preferred_room_type' => $validated['preferred_room_type'] ?? null,
            'priority_score' => 0,
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('room-registrations.index')
            ->with('success', 'Gửi đơn đăng ký chỗ ở thành công.');
    }

    public function show(Request $request, RoomRegistration $roomRegistration)
    {
        $this->ensureCanAccess($request, $roomRegistration);
        $roomRegistration->load(['student.user', 'reviewer']);

        return view('room-registrations.show', compact('roomRegistration'));
    }

    public function edit(Request $request, RoomRegistration $roomRegistration)
    {
        $this->ensureCanAccess($request, $roomRegistration);

        if ($roomRegistration->status !== 'pending') {
            return back()->with('error', 'Chỉ đơn đang chờ duyệt mới được chỉnh sửa.');
        }

        $user = $request->user();
        $students = $user->role === 'student'
            ? collect([$user->student->load('user')])
            : Student::with('user')->orderBy('student_code')->get();

        return view('room-registrations.edit', compact('roomRegistration', 'students'));
    }

    public function update(Request $request, RoomRegistration $roomRegistration)
    {
        $this->ensureCanAccess($request, $roomRegistration);

        if ($roomRegistration->status !== 'pending') {
            return back()->with('error', 'Chỉ đơn đang chờ duyệt mới được chỉnh sửa.');
        }

        $user = $request->user();
        $isStudent = $user->role === 'student';

        $validated = $request->validate([
            'student_id' => $isStudent ? ['nullable'] : ['required', 'exists:students,id'],
            'semester' => ['required', Rule::in(['1', '2', 'Hè'])],
            'academic_year' => ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'preferred_room_type' => ['nullable', Rule::in(['Phòng 4 người', 'Phòng 6 người', 'Phòng 8 người'])],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $studentId = $isStudent ? $user->student?->id : $validated['student_id'];

        $exists = RoomRegistration::query()
            ->where('student_id', $studentId)
            ->where('semester', $validated['semester'])
            ->where('academic_year', $validated['academic_year'])
            ->where('id', '<>', $roomRegistration->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'student_id' => 'Sinh viên đã có một đơn khác trong học kỳ này.',
            ]);
        }

        $roomRegistration->update([
            'student_id' => $studentId,
            'semester' => $validated['semester'],
            'academic_year' => $validated['academic_year'],
            'preferred_room_type' => $validated['preferred_room_type'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('room-registrations.show', $roomRegistration)
            ->with('success', 'Cập nhật đơn đăng ký thành công.');
    }

    public function destroy(Request $request, RoomRegistration $roomRegistration)
    {
        $this->ensureCanAccess($request, $roomRegistration);

        if ($roomRegistration->status !== 'pending') {
            return back()->with('error', 'Chỉ đơn đang chờ duyệt mới được hủy.');
        }

        $roomRegistration->delete();

        return redirect()->route('room-registrations.index')
            ->with('success', 'Đã hủy đơn đăng ký.');
    }

    public function approve(Request $request, RoomRegistration $roomRegistration)
    {
        if ($roomRegistration->status !== 'pending') {
            return back()->with('error', 'Đơn này đã được xử lý.');
        }

        $validated = $request->validate([
            'priority_score' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $roomRegistration->update([
            'priority_score' => $validated['priority_score'] ?? $roomRegistration->priority_score,
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Đã duyệt đơn đăng ký.');
    }

    public function reject(Request $request, RoomRegistration $roomRegistration)
    {
        if ($roomRegistration->status !== 'pending') {
            return back()->with('error', 'Đơn này đã được xử lý.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $roomRegistration->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Đã từ chối đơn đăng ký.');
    }

    private function ensureCanAccess(Request $request, RoomRegistration $roomRegistration): void
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'staff'], true)) {
            return;
        }

        if ($user->role === 'student' && $roomRegistration->student_id === $user->student?->id) {
            return;
        }

        abort(403, 'Bạn không có quyền xem hoặc sửa đơn này.');
    }
}
