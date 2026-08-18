<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')
                ->unique()
                ->constrained('room_registrations')
                ->restrictOnDelete();
            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();
            $table->foreignId('bed_id')
                ->constrained('beds')
                ->restrictOnDelete();
            $table->foreignId('allocated_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 30)->default('active');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['bed_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};
