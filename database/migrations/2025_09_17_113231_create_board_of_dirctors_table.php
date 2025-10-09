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
        Schema::create('board_of_dirctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('image_path');
            $table->text('description');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_of_dirctors');
    }
};
