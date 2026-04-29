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
            $table->string('first_name')->nullable()->after('id');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('extension_name')->nullable()->after('last_name');
            $table->string('sex')->nullable()->after('extension_name');
            $table->date('birthday')->nullable()->after('sex');
            $table->string('marital_status')->nullable()->after('birthday');
            $table->string('contact_person_emergency')->nullable()->after('contact_number');
            $table->string('contact_number_emergency')->nullable()->after('contact_person_emergency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'middle_name', 'last_name', 'extension_name',
                'sex', 'birthday', 'marital_status',
                'contact_person_emergency', 'contact_number_emergency'
            ]);
        });
    }
};
