<?php

namespace App\Observers;

use App\Models\Contract;
use App\Services\Payments\PaymentGeneration\PaymentGeneratorInterface;


/**
 * Contract Observer
 * 
 * Listens to Contract model events and handles:
 * - Generating payments when contract is created
 * - Validating unique active contract per unit
 * - Refreshing unit status when contract status changes
 */
class ContractObserver
{
    /**
     * Inject the payment generator via constructor.
     * Laravel will automatically resolve this from the container
     * based on the binding in AppServiceProvider.
     */
    public function __construct(
        private PaymentGeneratorInterface $generator
    ) {}

    /**
     * Handle the Contract "created" event.
     * 
     * Generates and saves payments for the new contract.
     * Only generates if contract status is 'active' or 'pending'.
     *
     * @param Contract $contract
     * @return void
     */
    public function created(Contract $contract): void
    {
        // Only generate payments for active or pending contracts
        if (!in_array($contract->status, ['active', 'pending'])) {
            return;
        }

        // Get payments data from generator
        $paymentsData = $this->generator->generate($contract);

        // Save payments and link to contract
        if (!empty($paymentsData)) {
            $contract->payments()->createMany($paymentsData);
        }

        // Refresh unit status
        $contract->unit->refreshStatus();
    }

    /**
     * Handle the Contract "updated" event.
     *
     * Refreshes unit status when contract status changes.
     * Regenerates payments when pending contract's payment-affecting fields change.
     *
     * @param Contract $contract
     * @return void
     */
    public function updated(Contract $contract): void
    {
        // Refresh unit status when contract status changes
        if ($contract->wasChanged('status')) {
            $contract->unit->refreshStatus();
        }

        // Regenerate payments if pending contract's payment-affecting fields changed
        if ($contract->status === 'pending' &&
            $contract->wasChanged(['beginning_date', 'end_date', 'total_amount', 'payment_plan'])) {
            $this->regeneratePayments($contract);
        }
    }

    /**
     * Regenerate all payments for a pending contract.
     * Only deletes non-paid payments as a safety measure.
     *
     * @param Contract $contract
     * @return void
     */
    private function regeneratePayments(Contract $contract): void
    {
        // Safety: only delete non-paid payments (should be all for pending contracts)
        $contract->payments()
            ->whereNotIn('status', ['paid'])
            ->forceDelete();

        // Generate new payments
        $paymentsData = $this->generator->generate($contract);

        if (!empty($paymentsData)) {
            $contract->payments()->createMany($paymentsData);
        }
    }

    /**
     * Handle the Contract "saving" event.
     * 
     * Runs before both create and update.
     * Validates that no other active/pending contract exists for the same unit.
     *
     * @param Contract $contract
     * @return void
     * @throws \InvalidArgumentException
     */
    public function saving(Contract $contract): void
    {
        $this->validateUniqueActiveContract($contract);
    }

    /**
     * Handle the Contract "deleted" event.
     * 
     * Refreshes unit status when contract is deleted.
     *
     * @param Contract $contract
     * @return void
     */
    public function deleted(Contract $contract): void
    {
        $contract->unit->refreshStatus();
    }

    /**
     * Validate that the unit doesn't have another active/pending contract.
     *
     * @param Contract $contract
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateUniqueActiveContract(Contract $contract): void
    {
        // Only validate for active/pending contracts
        if (!in_array($contract->status, ['active', 'pending'])) {
            return;
        }

        // Check if another active/pending contract exists for this unit
        $query = Contract::where('unit_id', $contract->unit_id)
            ->whereIn('status', ['active', 'pending']);

        // Exclude current contract if updating
        if ($contract->exists) {
            $query->where('id', '!=', $contract->id);
        }

        if ($query->exists()) {
            throw new \InvalidArgumentException(
                'This unit already has an active or pending contract.'
            );
        }
    }
}