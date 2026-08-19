<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_keyword', function (Blueprint $table) {
            $table->id();
            $table->string('desc', 50)->unique();
            $table->string('keyword', 255)->nullable();
            $table->string('agila_help', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_keyword');
    }
};
