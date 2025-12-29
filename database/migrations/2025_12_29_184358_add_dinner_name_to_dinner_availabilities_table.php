<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dinner_availabilities', function (Blueprint $table) {
            $table->string('dinner_name')->nullable()->after('can_host');
        });
    }

    public function down(): void
    {
        Schema::table('dinner_availabilities', function (Blueprint $table) {
            $table->dropColumn('dinner_name');
        });
    }
};
