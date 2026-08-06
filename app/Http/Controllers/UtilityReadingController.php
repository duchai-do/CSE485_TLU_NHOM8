<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\UtilityReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UtilityReadingController extends Controller
{
    public function index(Request $request)
    {
        $readings = UtilityReading::with('room.building')
            ->when($request->filled('month'), fn ($query) => $query->where('reading_month', $request->integer('month')))
            ->when($request->filled('year'), fn ($query) => $query->where('reading_year', $request->integer('year')))
            ->latest('reading_year')->latest('reading_month')->paginate(15)->withQueryString();

        return view('utility-readings.index', compact('readings'));
    }

    public function create()
    {
        return view('utility-readings.create', ['rooms' => Room::with('building')->orderBy('room_number')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')],
            'reading_month' => ['required', 'integer', 'between:1,12'],
            'reading_year' => ['required', 'integer', 'between:2000,2100'],
            'previous_electricity' => ['required', 'numeric', 'min:0'],
            'current_electricity' => ['required', 'numeric', 'gte:previous_electricity'],
            'previous_water' => ['required', 'numeric', 'min:0'],
            'current_water' => ['required', 'numeric', 'gte:previous_water'],
            'electricity_unit_price' => ['required', 'numeric', 'min:0'],
            'water_unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $exists = UtilityReading::where('room_id', $data['room_id'])
            ->where('reading_month', $data['reading_month'])
            ->where('reading_year', $data['reading_year'])->exists();
        if ($exists) {
            throw ValidationException::withMessages(['reading_month' => 'Phòng này đã có chỉ số trong tháng/năm đã chọn.']);
        }

        $latest = UtilityReading::where('room_id', $data['room_id'])
            ->orderByDesc('reading_year')->orderByDesc('reading_month')->first();
        if ($latest && ((float) $data['previous_electricity'] < (float) $latest->current_electricity
            || (float) $data['previous_water'] < (float) $latest->current_water)) {
            throw ValidationException::withMessages(['previous_electricity' => 'Chỉ số cũ không được nhỏ hơn chỉ số đã chốt gần nhất của phòng.']);
        }

        DB::transaction(function () use ($data): void {
            UtilityReading::create($data + ['recorded_by' => auth()->id(), 'recorded_at' => now()]);
        });

        return redirect()->route('utility-readings.index')->with('success', 'Đã lưu chỉ số điện nước.');
    }
}
