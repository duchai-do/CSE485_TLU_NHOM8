<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allocation_id')
                ->unique()
                ->constrained('allocations')
                ->restrictOnDelete();
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

            $table->index(['status', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
