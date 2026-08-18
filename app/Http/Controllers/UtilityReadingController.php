<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\UtilityReading;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UtilityReadingController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with('building')->orderBy('room_number')->get();
        $readings = UtilityReading::with('room.building')
            ->when($request->filled('room_id'), fn ($query) => $query->where('room_id', $request->integer('room_id')))
            ->when($request->filled('month'), fn ($query) => $query->where('reading_month', $request->integer('month')))
            ->when($request->filled('year'), fn ($query) => $query->where('reading_year', $request->integer('year')))
            ->latest('reading_year')->latest('reading_month')->paginate(15)->withQueryString();

        return view('utility-readings.index', compact('readings', 'rooms'));
    }

    public function create()
    {
        return view('utility-readings.create', ['rooms' => Room::with('building')->orderBy('room_number')->get(), 'reading' => new UtilityReading()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $this->ensureReadingIsValid($data);
        UtilityReading::create($data + ['recorded_by' => auth()->id(), 'recorded_at' => now()]);
        return redirect()->route('utility-readings.index')->with('success', 'Đã lưu chỉ số điện nước.');
    }

    public function edit(UtilityReading $utilityReading)
    {
        return view('utility-readings.create', ['rooms' => Room::with('building')->orderBy('room_number')->get(), 'reading' => $utilityReading]);
    }

    public function update(Request $request, UtilityReading $utilityReading)
    {
        $data = $this->validatedData($request);
        $this->ensureReadingIsValid($data, $utilityReading);
        $utilityReading->update($data);
        return redirect()->route('utility-readings.index')->with('success', 'Đã cập nhật chỉ số điện nước.');
    }

    public function destroy(UtilityReading $utilityReading)
    {
        $utilityReading->delete();
        return redirect()->route('utility-readings.index')->with('success', 'Đã xóa chỉ số điện nước.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
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
    }

    private function ensureReadingIsValid(array $data, ?UtilityReading $ignore = null): void
    {
        $samePeriod = UtilityReading::where('room_id', $data['room_id'])
            ->where('reading_month', $data['reading_month'])->where('reading_year', $data['reading_year'])
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))->exists();
        if ($samePeriod) {
            throw ValidationException::withMessages(['reading_month' => 'Phòng này đã có chỉ số trong tháng/năm đã chọn.']);
        }

        $previous = UtilityReading::where('room_id', $data['room_id'])
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->where(fn ($query) => $query->where('reading_year', '<', $data['reading_year'])
                ->orWhere(fn ($q) => $q->where('reading_year', $data['reading_year'])->where('reading_month', '<', $data['reading_month'])))
            ->orderByDesc('reading_year')->orderByDesc('reading_month')->first();
        if ($previous && ((float) $data['previous_electricity'] < (float) $previous->current_electricity
            || (float) $data['previous_water'] < (float) $previous->current_water)) {
            throw ValidationException::withMessages(['previous_electricity' => 'Chỉ số cũ không được nhỏ hơn chỉ số đã chốt kỳ trước.']);
        }
    }
}
