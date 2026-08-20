<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->string('color', 30)->nullable()->after('notification_keyword');
        });

        // Palette of distinct vibrant hex colors for clubs
        $colors = [
            '#EF4444', // Red
            '#3B82F6', // Blue
            '#10B981', // Emerald Green
            '#F59E0B', // Amber
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#06B6D4', // Cyan
            '#F97316', // Orange
            '#14B8A6', // Teal
            '#6366F1', // Indigo
            '#84CC16', // Lime
            '#D97706', // Yellow-Orange
            '#A855F7', // Violet
            '#0284C7', // Sky Blue
            '#E11D48', // Rose
        ];

        $clubs = DB::table('lib_club_name')->get();
        foreach ($clubs as $index => $club) {
            $assignedColor = $colors[$index % count($colors)];
            DB::table('lib_club_name')
                ->where('id', $club->id)
                ->update(['color' => $assignedColor]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lib_club_name', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
