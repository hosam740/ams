<?php

namespace app\Services\Payments\PaymentGeneration;

use App\Models\Contract;

/**
 * Interface for payment generation algorithms/strategies.
 * 
 * Any class that generates payments must implement this interface.
 * This allows swapping payment generation algorithms without changing
 * the code that uses them.
 */
interface PaymentGeneratorInterface
{
    /**
     * Generate payments data for a contract.
     * 
     * Returns an array of payment data ready to be inserted.
     * Does NOT save to database - that's the caller's responsibility.
     *
     * @param Contract $contract The contract to generate payments for
     * @return array Array of payment data arrays, each containing:
     *               - due_date: string
     *               - amount: float
     *               - status: string ('pending' or 'due')
     */
    public function generate(Contract $contract): array;
}