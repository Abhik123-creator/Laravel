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
        Schema::table('field_definitions', function (Blueprint $table) {
            $table->json('options')->nullable()->after('type'); // For radio, checkbox, dropdown options
            $table->boolean('required')->default(true)->after('options');
            $table->text('description')->nullable()->after('required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_definitions', function (Blueprint $table) {
            $table->dropColumn(['options', 'required', 'description']);
        });
    }
};
