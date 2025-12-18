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
        Schema::table('dinner_groups', function (Blueprint $table) {
            $table->string('slogan')->nullable();
            $table->string('image')->nullable();
            $table->string('group_code', 32)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinner_groups', function (Blueprint $table) {
            $table->dropColumn(['slogan', 'image']);
            $table->string('group_code', 8)->change();
        });
    }
};
