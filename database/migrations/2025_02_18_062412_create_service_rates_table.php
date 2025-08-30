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
        Schema::create('service_rates', function (Blueprint $table) {
            $table->id('serviceRateID');
            $table->string('vehicleSizeCode');
            $table->unsignedBigInteger('serviceTypeID');
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->foreign('vehicleSizeCode')->references('vehicleSizeCode')->on('vehicle_sizes');
            $table->foreign('serviceTypeID')->references('serviceTypeID')->on('service_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_rates');
    }
};
