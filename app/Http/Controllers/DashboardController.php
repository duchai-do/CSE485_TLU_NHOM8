<?php

namespace App\Http\Controllers;

use App\Models\RoomRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'student') {
            $studentId = $user->student?->id ?? 0;
            $query = RoomRegistration::where('student_id', $studentId);

            return view('dashboard', [
                'isStudent' => true,
                'student' => $user->student,
                'userCount' => null,
                'studentCount' => null,
                'registrationCount' => (clone $query)->count(),
                'pendingCount' => (clone $query)->where('status', 'pending')->count(),
                'approvedCount' => (clone $query)->where('status', 'approved')->count(),
                'rejectedCount' => (clone $query)->where('status', 'rejected')->count(),
            ]);
        }

        return view('dashboard', [
            'isStudent' => false,
            'student' => null,
            'userCount' => User::count(),
            'studentCount' => Student::count(),
            'registrationCount' => RoomRegistration::count(),
            'pendingCount' => RoomRegistration::where('status', 'pending')->count(),
            'approvedCount' => RoomRegistration::where('status', 'approved')->count(),
            'rejectedCount' => RoomRegistration::where('status', 'rejected')->count(),
        ]);
    }
}
