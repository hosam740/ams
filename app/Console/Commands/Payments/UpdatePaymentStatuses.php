<?php

namespace App\Console\Commands\Payments;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePaymentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update payment statuses based on due dates (pending -> due -> overdue)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pendingToDue = $this->updatePendingToDue();
        $dueToOverdue = $this->updateDueToOverdue();
        $pendingToOverdue = $this->updatePendingToOverdue();

        $message = "Payment statuses updated: {$pendingToDue} pending->due, {$dueToOverdue} due->overdue, {$pendingToOverdue} pending->overdue";

        $this->info($message);
        Log::info($message);

        return Command::SUCCESS;
    }

    /**
     * Update payments from 'pending' to 'due'.
     * Applies to payments within DAYS_BEFORE_OVERDUE days of due_date.
     */
    private function updatePendingToDue(): int
    {
        $today = today();
        $daysBeforeOverdue = Payment::getDaysBeforeOverdue();

        return Payment::where('status', 'pending')
            ->where('due_date', '<=', $today->copy()->addDays($daysBeforeOverdue))
            ->where('due_date', '>=', $today)
            ->update(['status' => 'due']);
    }

    /**
     * Update payments from 'due' to 'overdue'.
     * Applies to payments past their due_date.
     */
    private function updateDueToOverdue(): int
    {
        return Payment::where('status', 'due')
            ->where('due_date', '<', today())
            ->update(['status' => 'overdue']);
    }

    /**
     * Update payments from 'pending' directly to 'overdue'.
     * Applies to payments that skipped the 'due' status and are past due_date.
     */
    private function updatePendingToOverdue(): int
    {
        return Payment::where('status', 'pending')
            ->where('due_date', '<', today())
            ->update(['status' => 'overdue']);
    }
}
