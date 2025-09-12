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
    {   // agent-asset relationship
        Schema::create('asset_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('agency_status')->default('1');
            $table->timestamp('assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_user');
    }
};
