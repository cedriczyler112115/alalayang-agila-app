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
        Schema::table('lib_region', function (Blueprint $table) {
            $table->string('logo')->nullable();
        });
        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->string('logo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lib_region', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
