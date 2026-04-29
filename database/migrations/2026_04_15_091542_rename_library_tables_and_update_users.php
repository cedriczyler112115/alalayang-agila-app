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
        // Rename regions to lib_region
        Schema::rename('regions', 'lib_region');
        
        // Rename clubs to lib_club_name
        Schema::rename('clubs', 'lib_club_name');
        
        // Update lib_club_name columns
        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->renameColumn('region_id', 'lib_region_id');
        });

        // Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('lib_region_id')->nullable()->after('status');
            $table->unsignedBigInteger('lib_club_name_id')->nullable()->after('lib_region_id');
            
            $table->foreign('lib_region_id')->references('id')->on('lib_region')->onDelete('set null');
            $table->foreign('lib_club_name_id')->references('id')->on('lib_club_name')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['lib_region_id']);
            $table->dropForeign(['lib_club_name_id']);
            $table->dropColumn(['lib_region_id', 'lib_club_name_id']);
        });

        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->renameColumn('lib_region_id', 'region_id');
        });

        Schema::rename('lib_club_name', 'clubs');
        Schema::rename('lib_region', 'regions');
    }
};
