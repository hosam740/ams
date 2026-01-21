<?php

namespace App\Services\Payments\PaymentGeneration;

use App\Models\Contract;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Simple Payment Generator
 * 
 * Generates payments following the contract start date.
 * Each payment is due on the same day of the month as the contract started.
 * 
 * Example: Contract starts Jan 15, monthly plan
 * Payments: Jan 15, Feb 15, Mar 15, etc.
 */
class SimplePaymentGenerator implements PaymentGeneratorInterface
{
    /**
     * Payment plan intervals in months
     */
    private const PLAN_MONTHS = [
        'monthly'    => 1,
        'quarterly'  => 3,
        'triannual'  => 4,
        'semiannual' => 6,
        'annually'   => 12,
    ];

    /**
     * Generate payments data for a contract.
     *
     * @param Contract $contract
     * @return array
     */
    public function generate(Contract $contract): array
    {
        // 1. Calculate all due dates for the contract period
        $allDueDates = $this->calculateAllDueDates($contract);

        if (empty($allDueDates)) {
            return [];
        }

        // 2. Calculate payment amount (total / number of payments)
        $paymentAmount = $this->calculatePaymentAmount(
            $contract->total_amount,
            count($allDueDates)
        );

        // 3. Filter out past dates and build payment data
        $payments = $this->buildPaymentsData(
            $allDueDates,
            $paymentAmount,
            $contract->total_amount
        );

        return $payments;
    }

    /**
     * Calculate all due dates for the entire contract period.
     * 
     * Starts from beginning_date and adds months based on payment plan
     * until we exceed end_date.
     *
     * @param Contract $contract
     * @return array Array of Carbon dates
     */
    private function calculateAllDueDates(Contract $contract): array
    {
        $dates = [];

        $startDate = Carbon::parse($contract->beginning_date);
        $endDate = Carbon::parse($contract->end_date);
        $monthsInterval = $this->getPlanMonths($contract->payment_plan);

        $currentDate = $startDate->copy();

        // Generate dates until we pass the end date
        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->copy();
            $currentDate->addMonths($monthsInterval);
        }

        return $dates;
    }

    /**
     * Get the number of months for a payment plan.
     *
     * @param string $plan
     * @return int
     */
    private function getPlanMonths(string $plan): int
    {
        return self::PLAN_MONTHS[$plan] ?? 1;
    }

    /**
     * Calculate the amount for each payment.
     *
     * @param float $totalAmount
     * @param int $numberOfPayments
     * @return float
     */
    private function calculatePaymentAmount(float $totalAmount, int $numberOfPayments): float
    {
        if ($numberOfPayments <= 0) {
            return 0;
        }

        return floor($totalAmount / $numberOfPayments * 100) / 100;
    }

    /**
     * Build payment data arrays, filtering out past due dates.
     *
     * - Skips dates before today
     * - Sets status to 'due' if within threshold days
     * - Sets status to 'pending' otherwise
     * - Last payment gets the remainder (to handle rounding)
     *
     * @param array $allDueDates
     * @param float $paymentAmount
     * @param float $totalAmount (used only for reference, actual total is calculated from future payments)
     * @return array
     */
    private function buildPaymentsData(
        array $allDueDates,
        float $paymentAmount,
        float $totalAmount
    ): array {
        $payments = [];
        $today = Carbon::today();
        $threshold = Payment::getDaysBeforeOverdue();

        // 1. Filter future dates only and reindex
        $futureDueDates = array_values(array_filter(
            $allDueDates,
            fn($date) => $date->gte($today)
        ));

        $totalFuturePayments = count($futureDueDates);
        if ($totalFuturePayments === 0) {
            return [];
        }

        // 2. Calculate total for future payments only (for remainder calculation)
        $futureTotal = $paymentAmount * $totalFuturePayments;
        $runningTotal = 0;

        // 3. Build payments from future dates
        foreach ($futureDueDates as $index => $dueDate) {
            $isLastPayment = ($index === $totalFuturePayments - 1);
            $amount = $isLastPayment
                ? $futureTotal - $runningTotal
                : $paymentAmount;

            $runningTotal += $amount;

            $status = $this->determineStatus($dueDate, $today, $threshold);

            $payments[] = [
                'payment_number' => $index + 1,
                'due_date'       => $dueDate->toDateString(),
                'amount'         => round($amount, 2),
                'status'         => $status,
            ];
        }

        return $payments;
    }

    /**
     * Determine payment status based on due date.
     *
     * @param Carbon $dueDate
     * @param Carbon $today
     * @param int $threshold Days before due when status becomes 'due'
     * @return string 'pending' or 'due'
     */
    private function determineStatus(Carbon $dueDate, Carbon $today, int $threshold): string
    {
        // If due date is within threshold days from today
        if ($dueDate->lte($today->copy()->addDays($threshold))) {
            return 'due';
        }

        return 'pending';
    }
}