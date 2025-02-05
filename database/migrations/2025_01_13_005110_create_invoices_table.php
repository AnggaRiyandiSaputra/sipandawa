<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('no_invoice');
            $table->string('name');
            $table->string('description')->nullable();
            $table->date('issued_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->boolean('is_paid')->default(0);
            $table->string('image')->nullable();
            $table->integer('sub_total')->default(0);
            $table->boolean('is_pajak')->default(0);
            $table->integer('diskon')->default(0);
            $table->integer('grand_total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
