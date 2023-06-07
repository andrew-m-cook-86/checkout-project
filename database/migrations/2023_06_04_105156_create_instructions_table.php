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
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->unsignedDecimal('amount', 10,2);
            $table->string('transaction_id')->unique()->nullable();
            $table->enum('status', ['COMPLETED', 'FAILED'])->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructions');
    }
};
