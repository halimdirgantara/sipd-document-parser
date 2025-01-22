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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_activity_id')->constrained('sub_activities')->onDelete('restrict');
            $table->string('account_code');
            $table->string('name');
            $table->text('specification')->nullable();
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('price', 20, 2);
            $table->decimal('tax', 20, 2);
            $table->decimal('total', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
