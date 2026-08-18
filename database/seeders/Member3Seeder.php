<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Member3Seeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AllocationSeeder::class,
            ContractSeeder::class,
            ViolationRecordSeeder::class,
        ]);
    }
}
