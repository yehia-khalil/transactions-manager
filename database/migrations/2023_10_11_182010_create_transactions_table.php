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
            $table->foreignId('transaction_category_id')->constrained();
            $table->foreignId('transaction_sub_category_id')->nullable()->constrained();
            $table->integer('amount');
            $table->foreignId('payer')->references('id')->on('users');
            $table->date('due_date');
            $table->integer('vat');
            $table->boolean('is_vat_inclusive');
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
