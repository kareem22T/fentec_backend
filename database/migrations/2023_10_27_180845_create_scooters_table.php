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
        Schema::create('scooters', function (Blueprint $table) {
            $table->id();
            $table->string('machine_no', 200);
            $table->string('token', 200);
            $table->string('longitude', 200);
            $table->string('latitude', 200);
            $table->string('battary_charge', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scooters');
    }
};
