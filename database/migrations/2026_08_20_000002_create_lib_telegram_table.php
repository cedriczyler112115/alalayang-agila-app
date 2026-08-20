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
        if (!Schema::hasTable('lib_telegram')) {
            Schema::create('lib_telegram', function (Blueprint $table) {
                $table->id();
                $table->integer('club_id')->nullable();
                $table->string('token', 255)->nullable();
                $table->string('link', 255)->nullable();
                $table->bigInteger('group_id')->nullable();
                $table->string('t_group_name', 50)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib_telegram');
    }
};
