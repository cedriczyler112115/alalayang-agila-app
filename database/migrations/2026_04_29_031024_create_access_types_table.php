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
        Schema::create('access_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('access_types')->insert([
            ['name' => 'Governor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Regional Officers', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Club Officers', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Member', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_types');
    }
};
