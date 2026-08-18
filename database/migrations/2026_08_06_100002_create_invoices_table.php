<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contract_id')
                ->constrained('contracts')
                ->cascadeOnDelete();

            $table->string('invoice_code', 50)->unique();
            $table->unsignedTinyInteger('billing_month');
            $table->unsignedSmallInteger('billing_year');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('status', 30)->default('unpaid');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'contract_id',
                'billing_month',
                'billing_year',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
