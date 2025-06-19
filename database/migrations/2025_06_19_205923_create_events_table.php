<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
