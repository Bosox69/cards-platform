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
        Schema::create('templates', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('department_id')->constrained()->onDelete('cascade');
    $table->text('description')->nullable();
    $table->string('background_front')->nullable();
    $table->string('background_back')->nullable();
    $table->text('back_content')->nullable();
    $table->boolean('is_active')->default(true);
    $table->float('logo_x')->nullable();
    $table->float('logo_y')->nullable();
    $table->float('logo_width')->nullable();
    $table->float('text_start_x')->nullable();
    $table->float('text_start_y')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
