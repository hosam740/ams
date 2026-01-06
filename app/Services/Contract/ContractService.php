<?php

namespace App\Services\Contract;

use App\Models\assets\Unit;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class ContractService
{
    /**
     * يحدد حالة العقد بناءً على التواريخ
     *
     * @param  array  $data
     * @return string
     * @throws InvalidArgumentException
     */
    public static function setContractStatusValue(array $data): string
    {
        if (empty($data['beginning_date']) || empty($data['end_date'])) {
            throw new InvalidArgumentException('beginning_date و end_date مطلوبة لتحديد حالة العقد.');
        }

        $today = Carbon::today();
        $begin = Carbon::parse($data['beginning_date']);
        $end   = Carbon::parse($data['end_date']);

        if ($end->lt($begin)) {
            throw new InvalidArgumentException('تاريخ نهاية العقد يجب أن يكون بعد أو يساوي تاريخ البداية.');
        }

        // في حال تم فسخ العقد
        if (!empty($data['ended_at'])) {
            $endedAt = Carbon::parse($data['ended_at']);
            if ($endedAt->lte($today)) {
                return 'terminated';
            }
        }

        // العقد لم يبدأ بعد
        if ($begin->gt($today)) {
            return 'pending';
        }

        // العقد ساري بين البداية والنهاية
        if ($today->between($begin, $end)) {
            return 'active';
        }

        // العقد انتهى
        return 'expired';
    }

    /**
     * Get the primary contract for a unit to display.
     * Priority: active > pending > latest
     *
     * @param Unit $unit - Unit with loaded contracts relationship
     * @return Contract|null
     */
    public function getPrimaryContract(Unit $unit): ?Contract
    {
        // Work with already loaded contracts (no additional queries)
        if ($unit->relationLoaded('contracts')) {
            return $unit->contracts->firstWhere('status', 'active')
                ?? $unit->contracts->firstWhere('status', 'pending')
                ?? $unit->contracts->first();
        }

        // If contracts not loaded, execute query
        return $unit->contracts()
            ->where('status', 'active')
            ->latest('beginning_date')
            ->first()
            ?? $unit->contracts()
                ->where('status', 'pending')
                ->latest('beginning_date')
                ->first()
            ?? $unit->contracts()
                ->latest('beginning_date')
                ->first();
    }

    /**
     * Get the active contract for a unit (if exists)
     *
     * @param Unit $unit
     * @return Contract|null
     */
    public function getActiveContract(Unit $unit): ?Contract
    {
        if ($unit->relationLoaded('contracts')) {
            return $unit->contracts->firstWhere('status', 'active');
        }

        return $unit->contracts()
            ->where('status', 'active')
            ->latest('beginning_date')
            ->first();
    }

    /**
     * Get the pending/future contract for a unit (if exists)
     *
     * @param Unit $unit
     * @return Contract|null
     */
    public function getPendingContract(Unit $unit): ?Contract
    {
        if ($unit->relationLoaded('contracts')) {
            return $unit->contracts->firstWhere('status', 'pending');
        }

        return $unit->contracts()
            ->where('status', 'pending')
            ->latest('beginning_date')
            ->first();
    }

    /**
     * Get all contracts except the primary one
     *
     * @param Unit $unit
     * @param Contract|null $primaryContract
     * @return Collection
     */
    public function getOtherContracts(Unit $unit, ?Contract $primaryContract): Collection
    {
        if (!$unit->relationLoaded('contracts')) {
            $unit->load('contracts.tenant');
        }

        return $unit->contracts->reject(function ($contract) use ($primaryContract) {
            return $primaryContract && $contract->id === $primaryContract->id;
        });
    }

    /**
     * Check if unit has any active or pending contracts
     *
     * @param Unit $unit
     * @return bool
     */
    public function hasActiveOrPendingContracts(Unit $unit): bool
    {
        if ($unit->relationLoaded('contracts')) {
            return $unit->contracts->contains(function ($contract) {
                return in_array($contract->status, ['active', 'pending']);
            });
        }

        return $unit->contracts()
            ->whereIn('status', ['active', 'pending'])
            ->exists();
    }

    /**
     * Get contract statistics for a unit
     *
     * @param Unit $unit
     * @return array
     */
    public function getContractStatistics(Unit $unit): array
    {
        if (!$unit->relationLoaded('contracts')) {
            $unit->load('contracts');
        }

        $contracts = $unit->contracts;

        return [
            'total' => $contracts->count(),
            'active' => $contracts->where('status', 'active')->count(),
            'pending' => $contracts->where('status', 'pending')->count(),
            'completed' => $contracts->where('status', 'completed')->count(),
            'cancelled' => $contracts->where('status', 'cancelled')->count(),
            'total_revenue' => $contracts->where('status', 'completed')->sum('total_amount'),
        ];
    }
}
