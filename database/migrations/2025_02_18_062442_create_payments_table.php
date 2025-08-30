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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('paymentID');
            $table->unsignedBigInteger('appointmentID');
            $table->dateTime('paymentDateTime');
            $table->decimal('amount', 8, 2);
            $table->string('paymentMethod');
            $table->string('transactionID')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('appointmentID')->references('appointmentID')->on('appointments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
