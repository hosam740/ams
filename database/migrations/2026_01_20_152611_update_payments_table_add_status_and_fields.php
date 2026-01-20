<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add new fields
            $table->string('payment_method')->nullable()->after('status');
            $table->foreignId('received_by')->nullable()->after('payment_method')
                ->constrained('users')->nullOnDelete();
        });

        // Convert enum to string for PostgreSQL compatibility
        // First, create a temporary column
        Schema::table('payments', function (Blueprint $table) {
            $table->string('status_new')->default('pending')->after('status');
        });

        // Copy data from old column to new
        DB::table('payments')->update(['status_new' => DB::raw('status')]);

        // Drop old column and rename new one
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['payment_method', 'received_by']);
        });

        // Revert status to enum (MySQL only)
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('status_old', ['pending', 'due', 'paid', 'overdue', 'cancelled'])
                ->default('pending')->after('amount');
        });

        DB::table('payments')->update(['status_old' => DB::raw('status')]);

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};
