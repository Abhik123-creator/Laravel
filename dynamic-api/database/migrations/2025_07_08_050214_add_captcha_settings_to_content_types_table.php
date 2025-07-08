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
        Schema::table('content_types', function (Blueprint $table) {
            $table->boolean('captcha_enabled')->default(false)->after('require_authentication');
            $table->string('captcha_difficulty')->default('medium')->after('captcha_enabled'); // easy, medium, hard
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_types', function (Blueprint $table) {
            $table->dropColumn(['captcha_enabled', 'captcha_difficulty']);
        });
    }
};
