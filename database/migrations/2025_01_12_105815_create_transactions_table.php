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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_finance_id')->constrained('categories_finances')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('total');
            $table->date('date_transaction');
            $table->string('image')->nullable();
            $table->string('no_projek')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
