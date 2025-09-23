<?php

namespace App\Services\Contract;

use Carbon\Carbon;
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
}
