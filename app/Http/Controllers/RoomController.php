<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('building')->withCount('beds')->get();
        $buildings = Building::all();
        return view('rooms_index', compact('rooms', 'buildings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => 'required|string|max:50',
            'type' => 'required|in:male,female,other',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,full,maintenance',
        ]);

        Room::create($request->all());
        return redirect()->route('rooms.index')->with('success', 'Thêm phòng thành công!');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        
        // Ràng buộc: Không xóa phòng nếu còn giường
        if ($room->beds()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa phòng vì vẫn còn giường bên trong!');
        }

        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Đã xóa phòng thành công!');
    }
}