<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Database\Seeder;

class BuildingRoomBedSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // TÒA A - KHU NAM
        // =========================
        $buildingA = Building::updateOrCreate(
            ['code' => 'TDA'],
            [
                'name' => 'Tòa A (Khu Nam)',
                'description' => 'Dành cho sinh viên nam',
                'status' => 'active',
            ]
        );

        // Phòng nam 4 người
        $this->seedRoom(
            $buildingA->id,
            'P101',
            'male',
            4,
            500000,
            [
                'G01' => 'occupied',
                'G02' => 'empty',
                'G03' => 'maintenance',
                'G04' => 'empty',
            ]
        );

        // Phòng nam 6 người
        $this->seedRoom(
            $buildingA->id,
            'P102',
            'male',
            6,
            450000
        );

        // BỔ SUNG phòng nam 4 người để luôn có giường demo
        $this->seedRoom(
            $buildingA->id,
            'P103',
            'male',
            4,
            500000
        );

        // BỔ SUNG thêm phòng nam 4 người
        $this->seedRoom(
            $buildingA->id,
            'P104',
            'male',
            4,
            500000
        );

        // =========================
        // TÒA B - KHU NỮ
        // =========================
        $buildingB = Building::updateOrCreate(
            ['code' => 'TDB'],
            [
                'name' => 'Tòa B (Khu Nữ)',
                'description' => 'Dành cho sinh viên nữ',
                'status' => 'active',
            ]
        );

        // Phòng nữ 4 người
        $this->seedRoom(
            $buildingB->id,
            'P201',
            'female',
            4,
            500000
        );

        // Phòng nữ 6 người
        $this->seedRoom(
            $buildingB->id,
            'P202',
            'female',
            6,
            450000
        );

        // BỔ SUNG thêm phòng nữ để demo không bị hết giường
        $this->seedRoom(
            $buildingB->id,
            'P203',
            'female',
            4,
            500000
        );

        $this->seedRoom(
            $buildingB->id,
            'P204',
            'female',
            6,
            450000
        );
    }

    private function seedRoom(
        int $buildingId,
        string $roomNumber,
        string $type,
        int $capacity,
        float|int $price,
        array $initialStatuses = []
    ): void {
        $room = Room::updateOrCreate(
            [
                'building_id' => $buildingId,
                'room_number' => $roomNumber,
            ],
            [
                'type' => $type,
                'capacity' => $capacity,
                'price' => $price,
                'status' => 'available',
            ]
        );

        $this->createBedsIfMissing(
            $room,
            $capacity,
            $initialStatuses
        );
    }

    private function createBedsIfMissing(
        Room $room,
        int $capacity,
        array $initialStatuses = []
    ): void {
        for ($i = 1; $i <= $capacity; $i++) {
            $bedNumber = 'G' . str_pad(
                (string) $i,
                2,
                '0',
                STR_PAD_LEFT
            );

            // Không ghi đè trạng thái nếu giường đã tồn tại.
            Bed::firstOrCreate(
                [
                    'room_id' => $room->id,
                    'bed_number' => $bedNumber,
                ],
                [
                    'status' => $initialStatuses[$bedNumber] ?? 'empty',
                ]
            );
        }
    }
}