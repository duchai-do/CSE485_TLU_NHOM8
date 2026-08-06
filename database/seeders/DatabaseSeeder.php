<?php

namespace Database\Seeders;

use App\Models\{Allocation, Bed, Building, Contract, Invoice, InvoiceItem, Room, RoomRegistration, Student, User, UtilityReading, ViolationRecord};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Seed a minimal, connected dormitory-management dataset. */
    public function run(): void
    {
        $manager = User::create([
            'name' => 'Quản lý KTX', 'email' => 'manager@dormitory.test',
            'password' => 'password', 'role' => 'manager',
        ]);
        $studentUser = User::create([
            'name' => 'Nguyễn Văn An', 'email' => 'an@student.test', 'password' => 'password',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id, 'student_code' => 'SV2026001',
            'date_of_birth' => '2006-01-15', 'gender' => 'male', 'class_name' => 'K68CNTT1',
            'faculty' => 'Công nghệ thông tin', 'phone' => '0900000001',
        ]);
        $building = Building::create([
            'name' => 'Ký túc xá Nam A', 'code' => 'A', 'total_floors' => 5, 'gender_type' => 'male',
        ]);
        $room = Room::create([
            'building_id' => $building->id, 'room_number' => '101', 'floor' => 1,
            'room_type' => 'standard', 'gender_type' => 'male', 'capacity' => 4, 'monthly_price' => 450000,
        ]);
        $bed = Bed::create(['room_id' => $room->id, 'bed_number' => 'A1', 'status' => 'occupied']);
        $registration = RoomRegistration::create([
            'student_id' => $student->id, 'semester' => 'HK1', 'academic_year' => '2026-2027',
            'preferred_room_type' => 'standard', 'status' => 'approved',
            'reviewed_by' => $manager->id, 'reviewed_at' => now(),
        ]);
        $allocation = Allocation::create([
            'registration_id' => $registration->id, 'student_id' => $student->id, 'bed_id' => $bed->id,
            'allocated_by' => $manager->id, 'start_date' => '2026-09-01',
        ]);
        $contract = Contract::create([
            'allocation_id' => $allocation->id, 'contract_code' => 'HD-2026-0001',
            'start_date' => '2026-09-01', 'end_date' => '2027-06-30', 'monthly_price' => 450000,
            'deposit_amount' => 450000, 'signed_at' => now(),
        ]);
        UtilityReading::create([
            'room_id' => $room->id, 'reading_month' => 9, 'reading_year' => 2026,
            'current_electricity' => 120, 'current_water' => 35, 'electricity_unit_price' => 3500,
            'water_unit_price' => 12000, 'recorded_by' => $manager->id, 'recorded_at' => now(),
        ]);
        $invoice = Invoice::create([
            'contract_id' => $contract->id, 'invoice_code' => 'HD-202609-0001', 'billing_month' => 9,
            'billing_year' => 2026, 'total_amount' => 450000, 'due_date' => '2026-09-15',
            'created_by' => $manager->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id, 'item_type' => 'room_fee',
            'description' => 'Tiền phòng tháng 09/2026', 'unit_price' => 450000, 'amount' => 450000,
        ]);
        ViolationRecord::create([
            'student_id' => $student->id, 'contract_id' => $contract->id, 'recorded_by' => $manager->id,
            'violation_date' => '2026-09-10', 'violation_type' => 'late_return',
            'description' => 'Về ký túc xá muộn', 'status' => 'resolved', 'resolved_at' => now(),
        ]);
    }
}
