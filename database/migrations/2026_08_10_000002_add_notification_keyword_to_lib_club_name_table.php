<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->string('notification_keyword', 255)->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->dropColumn('notification_keyword');
        });
    }
};
