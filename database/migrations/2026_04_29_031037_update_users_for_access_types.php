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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('access_type_id')->nullable()->after('is_admin')->constrained('access_types')->onDelete('set null');
        });

        Schema::dropIfExists('user_permissions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['access_type_id']);
            $table->dropColumn('access_type_id');
        });

        // We do not perfectly restore user_permissions here to save time
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('module');
            $table->boolean('allow_view')->default(false);
            $table->boolean('allow_add')->default(false);
            $table->boolean('allow_edit')->default(false);
            $table->boolean('allow_delete')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'module']);
        });
    }
};
