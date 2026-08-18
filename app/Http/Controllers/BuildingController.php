<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::withCount('rooms')->get();
        return view('buildings_index', compact('buildings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:buildings,code',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,maintenance',
        ]);

        Building::create($request->all());
        return redirect()->route('buildings.index')->with('success', 'Thêm tòa nhà thành công!');
    }

    public function destroy($id)
    {
        $building = Building::findOrFail($id);
        
        // Ràng buộc: Không xóa tòa nhà nếu còn phòng
        if ($building->rooms()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa tòa nhà vì vẫn còn phòng bên trong!');
        }

        $building->delete();
        return redirect()->route('buildings.index')->with('success', 'Đã xóa tòa nhà thành công!');
    }
}