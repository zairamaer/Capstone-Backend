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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id('reminderID');
            $table->unsignedBigInteger('appointmentID');
            $table->dateTime('reminderDateTime');
            $table->string('reminderType'); // e.g., 'email', 'sms'
            $table->text('message');
            $table->boolean('sent')->default(false);
            $table->timestamps();

            $table->foreign('appointmentID')->references('appointmentID')->on('appointments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
