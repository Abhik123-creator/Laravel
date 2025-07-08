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
            $table->text('description')->nullable()->after('slug');
            $table->boolean('is_active')->default(true)->after('description');
            $table->integer('api_rate_limit')->default(100)->after('is_active');
            $table->boolean('require_authentication')->default(false)->after('api_rate_limit');
            $table->json('settings')->nullable()->after('require_authentication');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_types', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'is_active',
                'api_rate_limit',
                'require_authentication',
                'settings'
            ]);
        });
    }
};
