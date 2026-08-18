<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->string('semester', 30);
            $table->string('academic_year', 20);
            $table->string('preferred_room_type', 50)->nullable();
            $table->unsignedInteger('priority_score')->default(0);
            $table->string('status', 30)->default('pending');
            $table->text('note')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'semester', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_registrations');
    }
};
