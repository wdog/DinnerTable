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
        Schema::rename('teams', 'dinner_groups');

        Schema::table('dinner_groups', function (Blueprint $table) {
            $table->renameColumn('team_code', 'group_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dinner_groups', function (Blueprint $table) {
            $table->renameColumn('group_code', 'team_code');
        });

        Schema::rename('dinner_groups', 'teams');
    }
};
