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

        $room101 = Room::updateOrCreate(
            [
                'building_id' => $buildingA->id,
                'room_number' => 'P101',
            ],
            [
                'type' => 'male',
                'capacity' => 4,
                'price' => 500000,
                'status' => 'available',
            ]
        );

        $this->createBedsIfMissing($room101, 4, [
            'G01' => 'occupied',
            'G02' => 'empty',
            'G03' => 'maintenance',
            'G04' => 'empty',
        ]);

        $room102 = Room::updateOrCreate(
            [
                'building_id' => $buildingA->id,
                'room_number' => 'P102',
            ],
            [
                'type' => 'male',
                'capacity' => 6,
                'price' => 450000,
                'status' => 'available',
            ]
        );

        $this->createBedsIfMissing($room102, 6);

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

        $room201 = Room::updateOrCreate(
            [
                'building_id' => $buildingB->id,
                'room_number' => 'P201',
            ],
            [
                'type' => 'female',
                'capacity' => 4,
                'price' => 500000,
                'status' => 'available',
            ]
        );

        $this->createBedsIfMissing($room201, 4);

        $room202 = Room::updateOrCreate(
            [
                'building_id' => $buildingB->id,
                'room_number' => 'P202',
            ],
            [
                'type' => 'female',
                'capacity' => 6,
                'price' => 450000,
                'status' => 'available',
            ]
        );

        $this->createBedsIfMissing($room202, 6);
    }

    private function createBedsIfMissing(
        Room $room,
        int $capacity,
        array $initialStatuses = []
    ): void {
        for ($i = 1; $i <= $capacity; $i++) {
            $bedNumber = 'G' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            // firstOrCreate để không ghi đè trạng thái giường
            // nếu giường đã được xếp cho sinh viên trước đó.
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