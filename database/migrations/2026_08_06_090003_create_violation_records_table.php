<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();
            $table->foreignId('contract_id')
                ->nullable()
                ->constrained('contracts')
                ->nullOnDelete();
            $table->foreignId('recorded_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->date('violation_date');
            $table->string('violation_type', 100);
            $table->text('description');
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->string('status', 30)->default('pending');
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index('violation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violation_records');
    }
};
