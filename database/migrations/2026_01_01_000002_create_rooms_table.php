<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
            $table->string('room_number');
            $table->enum('type', ['male', 'female', 'other'])->default('male');
            $table->integer('capacity'); // Sức chứa (tổng số giường tối đa)
            $table->decimal('price', 12, 2); // Giá phòng
            $table->enum('status', ['available', 'full', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};