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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('module');
            $table->boolean('allow_view')->default(false);
            $table->boolean('allow_add')->default(false);
            $table->boolean('allow_edit')->default(false);
            $table->boolean('allow_delete')->default(false);
            $table->timestamps();

            // A user can only have one permission record per module
            $table->unique(['user_id', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
