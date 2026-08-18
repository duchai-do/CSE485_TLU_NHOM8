<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UtilityInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Không tự seed UtilityReading/Invoice nữa.
         *
         * Lý do:
         * - Chỉ số điện nước là dữ liệu vận hành thực tế theo phòng/tháng.
         * - Hóa đơn chỉ được tạo khi phòng có sinh viên đang ở thực tế.
         * - Tránh sinh hóa đơn "ảo" sau migrate:fresh --seed.
         *
         * Hãy nhập chỉ số từ giao diện:
         * /utility-readings/create
         */
        $this->command?->info(
            'UtilityInvoiceSeeder: bỏ qua dữ liệu vận hành điện nước/hóa đơn.'
        );
    }
}