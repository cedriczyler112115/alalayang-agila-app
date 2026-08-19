<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('global_keyword', function (Blueprint $table) {
            if (!Schema::hasColumn('global_keyword', 'desc')) {
                $table->string('desc', 50)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('global_keyword', 'agila_help')) {
                $table->string('agila_help', 255)->nullable()->after('keyword');
            }
        });

        DB::table('global_keyword')->updateOrInsert(
            ['desc' => 'agila_help'],
            [
                'desc' => 'agila_help',
                'agila_help' => 'ALALAYANG-AGILA-TFOE-PE-2026',
            ]
        );
    }

    public function down(): void
    {
        Schema::table('global_keyword', function (Blueprint $table) {
            if (Schema::hasColumn('global_keyword', 'agila_help')) {
                $table->dropColumn('agila_help');
            }
        });
    }
};
