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
        Schema::create('sub_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('restrict');
            $table->string('name');
            $table->string('funding_source');
            $table->string('location');
            $table->string('execution_time');
            $table->string('target_group');
            $table->string('current_year');
            $table->decimal('current_year_allocation', 20, 2);
            $table->string('previous_year');
            $table->decimal('previous_year_allocation', 20, 2);
            $table->string('next_year');
            $table->decimal('next_year_allocation', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_activities');
    }
};
