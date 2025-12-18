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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->renameColumn('team_id', 'dinner_group_id');
            $table->foreign('dinner_group_id')->references('id')->on('dinner_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dinner_group_id']);
            $table->renameColumn('dinner_group_id', 'team_id');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }
};
