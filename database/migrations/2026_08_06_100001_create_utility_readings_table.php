<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_readings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')
                ->constrained('rooms')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('reading_month');
            $table->unsignedSmallInteger('reading_year');

            $table->decimal('previous_electricity', 12, 2)->default(0);
            $table->decimal('current_electricity', 12, 2)->default(0);
            $table->decimal('previous_water', 12, 2)->default(0);
            $table->decimal('current_water', 12, 2)->default(0);
            $table->decimal('electricity_unit_price', 12, 2)->default(0);
            $table->decimal('water_unit_price', 12, 2)->default(0);

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('recorded_at')->nullable();
            $table->timestamps();

            $table->unique([
                'room_id',
                'reading_month',
                'reading_year',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_readings');
    }
};
