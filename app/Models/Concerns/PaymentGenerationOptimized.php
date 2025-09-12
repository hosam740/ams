<?php

namespace App\Models\Concerns;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Payment Generation Service - Fixed Last Payment Proration
 * Upfront payments + proration for first AND last periods when needed.
 */
trait PaymentGenerationOptimized
{
    /** Cache */
    private ?Collection $cachedPaymentDates = null;
    private ?array $cachedPeriodInfo = null;

    /** الخطط المدعومة (عدد الأشهر لكل فترة) */
    private const SUPPORTED_PLANS = [
        'monthly'    => 1,
        'quarterly'  => 3,
        'semiannual' => 6,
        'triannual'  => 4,  // ثلاث مرات بالسنة (كل 4 أشهر)
        'annually'   => 12,
    ];

    /** أشهر بدايات الفترات */
    private const PERIOD_START_MONTHS = [
        'quarterly'  => [1, 4, 7, 10],
        'semiannual' => [1, 7],
        'triannual'  => [1, 5, 9],
        'annually'   => [1],
    ];

    /** توليد الدفعات: الدفع مُسبقًا (بداية العقد ثم بدايات الفترات الطبيعية) */
    public function generatePayments(int $startingPaymentNumber = 1): void
    {
        $this->validateContract();

        $paymentData = $this->buildPaymentSchedule($startingPaymentNumber);
        if (empty($paymentData)) return;

        Payment::insert($paymentData);
        $this->clearCache();
    }

    public function regeneratePayments(): void
    {
        DB::transaction(function () {
            $lastNumber = (int) ($this->payments()
                ->whereIn('status', ['paid', 'overdue'])
                ->max('payment_number') ?? 0);

            $this->payments()->where('status', 'pending')->delete();
            $this->generatePayments($lastNumber + 1);
        });

        $this->clearCache();
    }

    /** بناء جدول الدفعات الكامل (تواريخ + مبالغ) */
    private function buildPaymentSchedule(int $startingPaymentNumber): array
    {
        $dates = $this->getPaymentDates();
        $totalPayments = $dates->count();
        if ($totalPayments === 0) return [];

        $amounts = $this->calculateAllPaymentAmounts($totalPayments);
        $paymentData = [];

        foreach ($dates as $i => $date) {
            $paymentData[] = [
                'contract_id'    => $this->id,
                'payment_number' => $startingPaymentNumber + $i,
                'due_date'       => $date->toDateString(),
                'amount'         => round($amounts[$i], 2),
                'status'         => 'pending',
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        return $paymentData;
    }

    /** حساب جميع مبالغ الدفعات مع معالجة التجزئة للأول والأخير */
    private function calculateAllPaymentAmounts(int $totalPayments): array
    {
        $totalCents = (int) round(((float) $this->total_amount) * 100);
        
        if ($totalPayments === 1) {
            return [round((float) $this->total_amount, 2)];
        }

        $info = $this->getPeriodInfo();
        $dates = $this->getPaymentDates();
        
        // تحديد نوع التوزيع المطلوب
        $hasFirstProration = !$info['is_aligned'];
        $hasLastProration = $this->hasLastPeriodProration($dates);
        
        if (!$hasFirstProration && !$hasLastProration) {
            // توزيع متساوٍ تماماً
            return $this->distributeEqualPayments($totalCents, $totalPayments);
        }
        
        if ($hasFirstProration && !$hasLastProration) {
            // تجزئة الأول فقط (الكود الحالي)
            return $this->distributeWithFirstProration($totalCents, $totalPayments, $info);
        }
        
        if (!$hasFirstProration && $hasLastProration) {
            // تجزئة الأخير فقط
            return $this->distributeWithLastProration($totalCents, $totalPayments, $dates);
        }
        
        // تجزئة الأول والأخير معاً
        return $this->distributeWithBothProrations($totalCents, $totalPayments, $info, $dates);
    }

    /** فحص ما إذا كانت الدفعة الأخيرة تحتاج تجزئة */
    private function hasLastPeriodProration(Collection $dates): bool
    {
        if ($dates->count() < 2) return false;
        
        $end = Carbon::parse($this->end_date);
        $lastPaymentDate = $dates->last();
        $plan = $this->getNormalizedPlan();
        
        // احسب نهاية الفترة الطبيعية للدفعة الأخيرة
        $naturalPeriodEnd = $this->getNaturalPeriodEnd($lastPaymentDate, $plan);
        
        // إذا كانت نهاية العقد قبل نهاية الفترة الطبيعية، فهناك تجزئة
        return $end->lt($naturalPeriodEnd);
    }

    /** الحصول على نهاية الفترة الطبيعية لتاريخ معين */
    private function getNaturalPeriodEnd(Carbon $start, string $plan): Carbon
    {
        return match ($plan) {
            'monthly' => $start->copy()->endOfMonth(),
            'quarterly' => $start->copy()->endOfQuarter(),
            'semiannual' => $this->getEndOfHalfYear($start),
            'triannual' => $this->getEndOfFourMonthPeriod($start),
            'annually' => $start->copy()->endOfYear(),
            default => $start->copy()->endOfMonth(),
        };
    }

    /** توزيع متساوٍ مع تصحيح آخر دفعة بالسنت */
    private function distributeEqualPayments(int $totalCents, int $totalPayments): array
    {
        $regular = intdiv($totalCents, $totalPayments);
        $remainder = $totalCents % $totalPayments;

        $amounts = array_fill(0, $totalPayments, $regular / 100);
        if ($remainder > 0) {
            $amounts[$totalPayments - 1] += $remainder / 100;
        }
        return array_map(fn ($v) => round($v, 2), $amounts);
    }

    /** أول دفعة مُجزّأة + باقي الدفعات متساوية */
    private function distributeWithFirstProration(int $totalCents, int $totalPayments, array $info): array
    {
        $firstCents = (int) round($totalCents * $info['first_payment_ratio']);
        $remainingCents = $totalCents - $firstCents;
        $remainingPayments = $totalPayments - 1;

        $amounts = [$firstCents / 100];

        if ($remainingPayments > 0) {
            $regular = intdiv($remainingCents, $remainingPayments);
            $remainder = $remainingCents % $remainingPayments;

            for ($i = 1; $i < $totalPayments - 1; $i++) {
                $amounts[] = $regular / 100;
            }
            if ($totalPayments > 1) {
                $amounts[] = ($regular + $remainder) / 100;
            }
        }

        return array_map(fn ($v) => round($v, 2), $amounts);
    }

    /** آخر دفعة مُجزّأة + باقي الدفعات متساوية */
    private function distributeWithLastProration(int $totalCents, int $totalPayments, Collection $dates): array
    {
        $start = Carbon::parse($this->beginning_date);
        $end = Carbon::parse($this->end_date);
        $totalDays = $start->diffInDays($end) + 1;
        
        // احسب أيام الدفعة الأخيرة
        $lastPaymentStart = $dates->last();
        $lastDays = $lastPaymentStart->diffInDays($end) + 1;
        $lastRatio = $lastDays / $totalDays;
        
        $lastCents = (int) round($totalCents * $lastRatio);
        $remainingCents = $totalCents - $lastCents;
        $remainingPayments = $totalPayments - 1;
        
        $amounts = [];
        
        if ($remainingPayments > 0) {
            $regular = intdiv($remainingCents, $remainingPayments);
            $remainder = $remainingCents % $remainingPayments;
            
            // الدفعات العادية
            for ($i = 0; $i < $remainingPayments - 1; $i++) {
                $amounts[] = $regular / 100;
            }
            // ما قبل الأخيرة تأخذ الباقي من التوزيع العادي
            if ($remainingPayments > 0) {
                $amounts[] = ($regular + $remainder) / 100;
            }
        }
        
        // الدفعة الأخيرة المجزأة
        $amounts[] = $lastCents / 100;
        
        return array_map(fn ($v) => round($v, 2), $amounts);
    }

    /** أول وآخر دفعة مُجزّأتان + الوسط متساوي */
    private function distributeWithBothProrations(int $totalCents, int $totalPayments, array $info, Collection $dates): array
    {
        $start = Carbon::parse($this->beginning_date);
        $end = Carbon::parse($this->end_date);
        $totalDays = $start->diffInDays($end) + 1;
        
        // نسبة الدفعة الأولى (من الكود الموجود)
        $firstRatio = $info['first_payment_ratio'];
        
        // نسبة الدفعة الأخيرة
        $lastPaymentStart = $dates->last();
        $lastDays = $lastPaymentStart->diffInDays($end) + 1;
        $lastRatio = $lastDays / $totalDays;
        
        $firstCents = (int) round($totalCents * $firstRatio);
        $lastCents = (int) round($totalCents * $lastRatio);
        
        $remainingCents = $totalCents - $firstCents - $lastCents;
        $middlePayments = $totalPayments - 2; // اطرح الأول والأخير
        
        $amounts = [$firstCents / 100];
        
        if ($middlePayments > 0) {
            $regular = intdiv($remainingCents, $middlePayments);
            $remainder = $remainingCents % $middlePayments;
            
            // الدفعات الوسطى
            for ($i = 0; $i < $middlePayments - 1; $i++) {
                $amounts[] = $regular / 100;
            }
            // ما قبل الأخيرة تأخذ الباقي
            $amounts[] = ($regular + $remainder) / 100;
        }
        
        // الدفعة الأخيرة المجزأة
        $amounts[] = $lastCents / 100;
        
        return array_map(fn ($v) => round($v, 2), $amounts);
    }

    /** تواريخ الدفع (بداية العقد، ثم بدايات الفترات) مع caching */
    private function getPaymentDates(): Collection
    {
        if ($this->cachedPaymentDates) return $this->cachedPaymentDates;
        return $this->cachedPaymentDates = $this->calculatePaymentDatesOptimized();
    }

    /** حساب تواريخ الدفع محسّن */
    private function calculatePaymentDatesOptimized(): Collection
    {
        $start = Carbon::parse($this->beginning_date);
        $end = Carbon::parse($this->end_date);
        $plan = $this->getNormalizedPlan();

        if ($start->gt($end)) return collect();

        $dates = collect([$start->copy()]);
        $current = $start->copy();

        $guard = 0;
        while ($guard++ < 600) {
            $current = $this->getNextPeriodStart($current, $plan);
            if ($current->gt($end)) break;
            $dates->push($current->copy());
        }

        return $dates;
    }

    /** معلومات الفترة (alignment + نسبة أول دفعة) مع caching */
    private function getPeriodInfo(): array
    {
        if ($this->cachedPeriodInfo) return $this->cachedPeriodInfo;

        $start = Carbon::parse($this->beginning_date);
        $end   = Carbon::parse($this->end_date);
        $plan  = $this->getNormalizedPlan();

        $isAligned = $this->isStartOfBillingPeriod($start, $plan);
        $ratio = 1.0;

        if (!$isAligned) {
            $totalDays = $start->diffInDays($end) + 1;

            // استخدم تاريخ الدفعة التالية - 1 يوم كحد علوي
            $dates = $this->getPaymentDates();
            if ($dates->count() >= 2) {
                $firstPeriodEnd = $dates[1]->copy()->subDay();
            } else {
                // احتياطي (لن يحدث غالبًا): نهاية الفترة الطبيعية أو نهاية العقد أيهما أسبق
                $firstPeriodEnd = $this->getProrationEndForStart($start, $plan);
            }

            if ($firstPeriodEnd->gt($end)) $firstPeriodEnd = $end->copy();

            $firstDays = $start->diffInDays($firstPeriodEnd) + 1;
            $ratio = $firstDays / $totalDays;
        }

        return $this->cachedPeriodInfo = [
            'is_aligned' => $isAligned,
            'first_payment_ratio' => $ratio,
        ];
    }

    /** فحص العقد */
    private function validateContract(): void
    {
        $errors = [];

        if (empty($this->beginning_date) || empty($this->end_date)) {
            $errors[] = 'Contract dates are required';
        }

        if ((float) $this->total_amount <= 0) {
            $errors[] = 'Contract amount must be positive';
        }

        if (!empty($this->beginning_date) && !empty($this->end_date)) {
            $start = Carbon::parse($this->beginning_date);
            $end = Carbon::parse($this->end_date);
            if ($start->gt($end)) {
                $errors[] = 'Start date must be before or equal to end date';
            }
            if (!$end->isEndOfMonth()) {
                $errors[] = 'End date must be end of month';
            }
        }

        if (!$this->isValidPlan()) {
            $errors[] = 'Invalid payment plan: ' . ($this->payment_plan ?? '');
        }

        if ($errors) {
            throw new \InvalidArgumentException('Contract validation failed: ' . implode(', ', $errors));
        }
    }

    private function isValidPlan(): bool
    {
        return array_key_exists($this->getNormalizedPlan(), self::SUPPORTED_PLANS);
    }

    private function getNormalizedPlan(): string
    {
        return strtolower(trim($this->payment_plan ?? 'monthly'));
    }

    /** هل التاريخ بداية فترة طبيعية للخطة؟ */
    private function isStartOfBillingPeriod(Carbon $date, string $plan): bool
    {
        if ($date->day !== 1) return false;

        return match ($plan) {
            'monthly' => true,
            'annually' => $date->month === 1,
            default => in_array($date->month, self::PERIOD_START_MONTHS[$plan] ?? [], true),
        };
    }

    /** بداية الفترة التالية */
    private function getNextPeriodStart(Carbon $current, string $plan): Carbon
    {
        return match ($plan) {
            'monthly' => $current->copy()->addMonthsNoOverflow(1)->startOfMonth(),
            'quarterly' => $this->getNextQuarterStart($current),
            'semiannual' => $this->getNextSemiannualStart($current),
            'triannual' => $this->getNextTriannualStart($current),
            'annually' => $current->copy()->addYear()->startOfYear(),
            default => $current->copy()->addMonthsNoOverflow(1)->startOfMonth(),
        };
    }

    /** نهاية أول فترة طبيعية تحتوي تاريخ البداية */
    private function getProrationEndForStart(Carbon $start, string $plan): Carbon
    {
        return match ($plan) {
            'monthly' => $start->copy()->endOfMonth(),
            'quarterly' => $start->copy()->endOfQuarter(),
            'semiannual' => $this->getEndOfHalfYear($start),
            'triannual' => $this->getEndOfFourMonthPeriod($start),
            'annually' => $start->copy()->endOfYear(),
            default => $start->copy()->endOfMonth(),
        };
    }

    /** حساب أيام كل دفعة بدقة */
    private function calculatePaymentDays(Collection $dates): array
    {
        $start = Carbon::parse($this->beginning_date);
        $end = Carbon::parse($this->end_date);
        $paymentDays = [];
        
        foreach ($dates as $index => $paymentDate) {
            if ($index === 0) {
                // الدفعة الأولى: من بداية العقد
                $periodStart = $start->copy();
            } else {
                // باقي الدفعات: من تاريخ استحقاق الدفعة
                $periodStart = $paymentDate->copy();
            }
            
            if ($index === $dates->count() - 1) {
                // الدفعة الأخيرة: حتى نهاية العقد
                $periodEnd = $end->copy();
            } else {
                // باقي الدفعات: حتى يوم قبل الدفعة التالية
                $periodEnd = $dates[$index + 1]->copy()->subDay();
            }
            
            $days = $periodStart->diffInDays($periodEnd) + 1;
            $paymentDays[] = $days;
        }
        
        return $paymentDays;
    }

    // ===== Helpers =====

    private function getNextQuarterStart(Carbon $date): Carbon
    {
        $nextQuarter = $date->quarter + 1;
        if ($nextQuarter > 4) {
            return Carbon::create($date->year + 1, 1, 1);
        }
        return Carbon::create($date->year, ($nextQuarter - 1) * 3 + 1, 1);
    }

    private function getNextSemiannualStart(Carbon $date): Carbon
    {
        $nextMonth = $date->month <= 6 ? 7 : 1;
        $nextYear = $date->month <= 6 ? $date->year : $date->year + 1;
        return Carbon::create($nextYear, $nextMonth, 1);
    }

    private function getNextTriannualStart(Carbon $date): Carbon
    {
        $currentPeriod = (int) ceil($date->month / 4);
        if ($currentPeriod >= 3) {
            return Carbon::create($date->year + 1, 1, 1);
        }
        return Carbon::create($date->year, $currentPeriod * 4 + 1, 1);
    }

    private function getEndOfHalfYear(Carbon $date): Carbon
    {
        $endMonth = $date->month <= 6 ? 6 : 12;
        return Carbon::create($date->year, $endMonth, 1)->endOfMonth();
    }

    private function getEndOfFourMonthPeriod(Carbon $date): Carbon
    {
        $periodIndex = (int) ceil($date->month / 4);
        $endMonth = min($periodIndex * 4, 12);
        return Carbon::create($date->year, $endMonth, 1)->endOfMonth();
    }

    private function clearCache(): void
    {
        $this->cachedPaymentDates = null;
        $this->cachedPeriodInfo = null;
    }

    // ===== Debug / Preview =====

    /** معاينة الدفعات مع تفاصيل التجزئة */
    public function getPaymentPreview(): array
    {
        $dates = $this->getPaymentDates();
        $amounts = $this->calculateAllPaymentAmounts($dates->count());

        return $dates->map(function (Carbon $date, int $idx) use ($amounts, $dates) {
            $isFirst = $idx === 0;
            $isLast = $idx === $dates->count() - 1;
            
            $coverage = $this->getPaymentCoverage($date, $idx, $dates);
            
            return [
                'payment_number' => $idx + 1,
                'due_date' => $date->toDateString(),
                'amount' => round($amounts[$idx], 2),
                'day_of_month' => $date->day,
                'is_period_start' => $this->isStartOfBillingPeriod($date, $this->getNormalizedPlan()),
                'is_first_payment' => $isFirst,
                'is_last_payment' => $isLast,
                'coverage_days' => $coverage['days'],
                'coverage_period' => $coverage['period'],
                'is_prorated' => $coverage['is_prorated']
            ];
        })->toArray();
    }

    /** حساب تغطية كل دفعة */
    private function getPaymentCoverage(Carbon $paymentDate, int $index, Collection $dates): array
    {
        $start = $index === 0 ? Carbon::parse($this->beginning_date) : $paymentDate;
        
        if ($index === $dates->count() - 1) {
            // الدفعة الأخيرة
            $end = Carbon::parse($this->end_date);
        } else {
            // الدفعة العادية - تغطي حتى بداية الدفعة التالية - 1
            $end = $dates[$index + 1]->copy()->subDay();
        }
        
        $days = $start->diffInDays($end) + 1;
        $naturalEnd = $this->getNaturalPeriodEnd($paymentDate, $this->getNormalizedPlan());
        
        return [
            'days' => $days,
            'period' => $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d'),
            'is_prorated' => $end->lt($naturalEnd) || ($index === 0 && !$this->isStartOfBillingPeriod($start, $this->getNormalizedPlan()))
        ];
    }

    /** إحصائيات شاملة */
    public function getPaymentStats(): array
    {
        $dates = $this->getPaymentDates();
        $amounts = $this->calculateAllPaymentAmounts($dates->count());
        $count = count($amounts);
        $sum = array_sum($amounts);

        $info = $this->getPeriodInfo();
        $hasLastProration = $this->hasLastPeriodProration($dates);

        return [
            'total_payments' => $count,
            'total_amount' => round($sum, 2),
            'average_payment' => $count ? round($sum / $count, 2) : 0.0,
            'first_payment' => $amounts[0] ?? 0.0,
            'last_payment' => $count ? $amounts[$count - 1] : 0.0,
            'has_first_proration' => !$info['is_aligned'],
            'has_last_proration' => $hasLastProration,
            'contract_days' => Carbon::parse($this->beginning_date)->diffInDays(Carbon::parse($this->end_date)) + 1,
            'payment_plan' => $this->getNormalizedPlan()
        ];
    }
}