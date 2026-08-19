<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lib_region', function (Blueprint $table) {
            $table->string('notification_keyword', 100)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('lib_region', function (Blueprint $table) {
            $table->dropColumn('notification_keyword');
        });
    }
};
