<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('user');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                    ->orWhere('class_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('faculty')) {
            $query->where('faculty', $request->faculty);
        }

        $students = $query->latest()->paginate(10)->withQueryString();

        $faculties = Student::query()
            ->whereNotNull('faculty')
            ->where('faculty', '<>', '')
            ->distinct()
            ->orderBy('faculty')
            ->pluck('faculty');

        return view('students.index', compact('students', 'faculties'));
    }

    public function create()
    {
        $users = User::query()
            ->where('role', 'student')
            ->where('status', true)
            ->whereDoesntHave('student')
            ->orderBy('name')
            ->get();

        return view('students.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('students', 'user_id'),
            ],
            'student_code' => ['required', 'string', 'max:30', 'unique:students,student_code'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', Rule::in(['Nam', 'Nữ', 'Khác'])],
            'class_name' => ['nullable', 'string', 'max:100'],
            'faculty' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'priority_type' => ['nullable', 'string', 'max:100'],
        ]);

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Thêm hồ sơ sinh viên thành công.');
    }

    public function show(Student $student)
    {
        $student->load([
            'user',
            'roomRegistrations' => fn ($q) => $q->latest(),
        ]);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $users = User::query()
            ->where('role', 'student')
            ->where('status', true)
            ->where(function ($q) use ($student) {
                $q->where('id', $student->user_id)
                    ->orWhereDoesntHave('student');
            })
            ->orderBy('name')
            ->get();

        return view('students.edit', compact('student', 'users'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('students', 'user_id')->ignore($student->id),
            ],
            'student_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('students', 'student_code')->ignore($student->id),
            ],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', Rule::in(['Nam', 'Nữ', 'Khác'])],
            'class_name' => ['nullable', 'string', 'max:100'],
            'faculty' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'priority_type' => ['nullable', 'string', 'max:100'],
        ]);

        $student->update($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Cập nhật hồ sơ sinh viên thành công.');
    }

    public function destroy(Student $student)
    {
        if ($student->roomRegistrations()->exists()) {
            return back()->with(
                'error',
                'Không thể xóa sinh viên đã có đơn đăng ký chỗ ở.'
            );
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Đã xóa hồ sơ sinh viên.');
    }
}
