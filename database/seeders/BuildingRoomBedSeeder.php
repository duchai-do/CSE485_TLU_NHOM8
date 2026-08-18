<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Room;
use App\Models\Bed;

class BuildingRoomBedSeeder extends Seeder
{
    public function run(): void
    {
        $buildingA = Building::create([
            'code' => 'TDA',
            'name' => 'Tòa A (Khu Nam)',
            'description' => 'Dành cho sinh viên nam',
            'status' => 'active',
        ]);

        $room101 = Room::create([
            'building_id' => $buildingA->id,
            'room_number' => 'P101',
            'type' => 'male',
            'capacity' => 4,
            'price' => 500000,
            'status' => 'available',
        ]);

        Bed::create(['room_id' => $room101->id, 'bed_number' => 'G01', 'status' => 'occupied']);
        Bed::create(['room_id' => $room101->id, 'bed_number' => 'G02', 'status' => 'empty']);
        Bed::create(['room_id' => $room101->id, 'bed_number' => 'G03', 'status' => 'maintenance']);
        Bed::create(['room_id' => $room101->id, 'bed_number' => 'G04', 'status' => 'empty']);
    }
}