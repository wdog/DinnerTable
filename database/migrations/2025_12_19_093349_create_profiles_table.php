<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('house_number', 10)->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->unsignedInteger('max_guests')->nullable();
            $table->timestamp('privacy_accepted_at')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
