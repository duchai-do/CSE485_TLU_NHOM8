<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Http\Request;

class BedController extends Controller
{
    public function index()
    {
        $beds = Bed::with('room.building')->get();
        $rooms = Room::with('building')->get();
        return view('beds_index', compact('beds', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|string|max:50',
            'status' => 'required|in:empty,occupied,maintenance',
        ]);

        $room = Room::findOrFail($request->room_id);
        
        // Ràng buộc: Số lượng giường không được vượt quá sức chứa (capacity) của phòng
        if ($room->beds()->count() >= $room->capacity) {
            return redirect()->back()->with('error', 'Số lượng giường đã đạt tối đa sức chứa của phòng này!');
        }

        Bed::create($request->all());
        return redirect()->route('beds.index')->with('success', 'Thêm giường thành công!');
    }

    public function destroy($id)
    {
        $bed = Bed::findOrFail($id);
        
        // Ràng buộc: Không xóa giường đang có người ở (occupied)
        if ($bed->status === 'occupied') {
            return redirect()->back()->with('error', 'Không thể xóa giường đang có người sử dụng!');
        }

        $bed->delete();
        return redirect()->route('beds.index')->with('success', 'Đã xóa giường thành công!');
    }
}