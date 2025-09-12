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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255);
            $table->enum('type', ['villa', 'apartment', 'office', 'warehouse', 'store']);
            $table->string('description');
            $table->decimal('area', 10, 2);
            $table->enum('status', ['available', 'sold', 'rented', 'under_maintenance'])->default('available');

            // property/unit relationship
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
