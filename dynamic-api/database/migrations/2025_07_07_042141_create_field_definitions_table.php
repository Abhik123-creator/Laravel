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
        Schema::create('field_definitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_type_id')->constrained()->onDelete('cascade');

            $table->string('name');        // field machine name like "age"
            $table->string('label');       // field label like "Age"
            $table->string('type');        // string, integer, boolean, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_definitions');
    }
};
