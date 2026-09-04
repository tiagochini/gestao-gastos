<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('financial_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->date('reference_month');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status', 20)->default('pendente');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'reference_month']);
            $table->index(['reference_month', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_payments');
    }
};
