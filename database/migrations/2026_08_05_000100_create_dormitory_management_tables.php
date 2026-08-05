<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('student_code', 30)->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20);
            $table->string('class_name', 100)->nullable();
            $table->string('faculty', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('priority_type', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 30)->unique();
            $table->string('address')->nullable();
            $table->unsignedInteger('total_floors')->default(1);
            $table->string('gender_type', 20);
            $table->string('status', 30)->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('room_number', 30);
            $table->unsignedInteger('floor');
            $table->string('room_type', 50)->nullable();
            $table->string('gender_type', 20);
            $table->unsignedInteger('capacity');
            $table->decimal('monthly_price', 12, 2)->default(0);
            $table->string('status', 30)->default('available');
            $table->timestamps();
            $table->unique(['building_id', 'room_number']);
        });

        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('bed_number', 30);
            $table->string('status', 30)->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['room_id', 'bed_number']);
        });

        Schema::create('room_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('semester', 30);
            $table->string('academic_year', 20);
            $table->string('preferred_room_type', 50)->nullable();
            $table->integer('priority_score')->default(0);
            $table->string('status', 30)->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'semester', 'academic_year']);
        });

        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained('room_registrations')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bed_id')->constrained()->restrictOnDelete();
            $table->foreignId('allocated_by')->constrained('users')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 30)->default('active');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allocation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('contract_code', 50)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_price', 12, 2);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->string('status', 30)->default('active');
            $table->dateTime('signed_at')->nullable();
            $table->dateTime('terminated_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('utility_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('reading_month');
            $table->unsignedSmallInteger('reading_year');
            foreach (['previous_electricity', 'current_electricity', 'previous_water', 'current_water', 'electricity_unit_price', 'water_unit_price'] as $column) $table->decimal($column, 12, 2)->default(0);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('recorded_at')->nullable();
            $table->timestamps();
            $table->unique(['room_id', 'reading_month', 'reading_year']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_code', 50)->unique();
            $table->unsignedTinyInteger('billing_month');
            $table->unsignedSmallInteger('billing_year');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('status', 30)->default('unpaid');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['contract_id', 'billing_month', 'billing_year']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 50);
            $table->string('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('violation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->date('violation_date');
            $table->string('violation_type', 100);
            $table->text('description');
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->string('status', 30)->default('pending');
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['violation_records', 'invoice_items', 'invoices', 'utility_readings', 'contracts', 'allocations', 'room_registrations', 'beds', 'rooms', 'buildings', 'students'] as $table) Schema::dropIfExists($table);
    }
};
