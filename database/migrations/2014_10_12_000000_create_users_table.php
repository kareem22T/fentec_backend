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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->date('dob')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('identity_path')->nullable();
            $table->string('last_code')->nullable();
            $table->boolean('verify')->default(0);
            $table->boolean('approved')->default(0);
            $table->boolean('approving_msg_seen')->default(0);
            $table->boolean('rejected')->default(0);
            $table->text('rejection_reason')->nullable();
            $table->boolean('where_know')->nullable();
            $table->string('password');
            $table->integer('coins')->default(0);
            $table->string('notification_token', 100)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
