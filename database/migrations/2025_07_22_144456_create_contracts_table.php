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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            // tenant/contract relationship
            $table->foreignId('tenant_id')->constrained('tenants');

            $table->date('beginning_date');
            $table->date('end_date');
            $table->decimal('total_amount', 20, 2);
            $table->enum('payment_plan', ['monthly', 'quarterly', 'triannual', 'semiannual', 'annually'])->default('monthly');
            $table->boolean('active')->default(true);
            $table->date('ended_at')->nullable();

            // contract/unit relationship
            $table->foreignId('unit_id')->constrained('units');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
