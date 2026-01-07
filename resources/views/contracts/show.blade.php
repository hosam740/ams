@extends('layouts.app')
@section('title','عرض العقد')

@section('content')
    @include('components.global-errors')

    @php
        // ============================================
        // Data Preparation Section
        // All relationships are eager-loaded in controller
        // No additional queries will be executed here
        // ============================================

        // 1. Extract Related Models (already loaded)
        $tenant = $contract->tenant;
        $unit = $contract->unit;
        $property = $unit?->property;
        $asset = $property?->asset;
        $payments = $contract->payments; // Already loaded & sorted

        // 2. Status Badges Mapping
        $contractStatusBadges = [
            'active'     => 'success',
            'pending'    => 'warning',
            'expired'    => 'secondary',
            'terminated' => 'danger',
        ];
        $contractStatusBadge = $contractStatusBadges[$contract->status] ?? 'secondary';

        $paymentStatusBadges = [
            'pending'   => 'warning',
            'paid'      => 'success',
            'overdue'   => 'danger',
            'cancelled' => 'secondary',
        ];

        $paymentStatusLabels = [
            'pending'   => 'قيد الانتظار',
            'paid'      => 'مدفوعة',
            'overdue'   => 'متأخرة',
            'cancelled' => 'ملغاة',
        ];

        // 3. Duration Calculations (pure PHP, no queries)
        $beginDate = \Carbon\Carbon::parse($contract->beginning_date);
        $endDate = \Carbon\Carbon::parse($contract->end_date);
        $today = \Carbon\Carbon::today();

        $durationMonths = $beginDate->diffInMonths($endDate);
        $durationDays = $beginDate->diffInDays($endDate);
        $daysRemaining = $today->diffInDays($endDate, false); // negative if expired

        // 4. Financial Statistics (using collection methods, no queries)
        $totalAmount = $contract->total_amount;
        $paidAmount = $payments->where('status', 'paid')->sum('paid_amount');
        $remainingAmount = $totalAmount - $paidAmount;
        $progressPercentage = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100, 1) : 0;

        // 5. Payment Statistics (using collection methods, no queries)
        $totalPayments = $payments->count();
        $paidPayments = $payments->where('status', 'paid')->count();
        $pendingPayments = $payments->where('status', 'pending')->count();
        $overduePayments = $payments->where('status', 'overdue')->count();
        $cancelledPayments = $payments->where('status', 'cancelled')->count();

        // 6. Payment Plan Display
        $paymentPlanLabels = [
            'monthly'    => 'شهري',
            'quarterly'  => 'ربع سنوي',
            'triannual'  => 'ثلاثي سنوي',
            'semiannual' => 'نصف سنوي',
            'annually'   => 'سنوي',
        ];
        $paymentPlanLabel = $paymentPlanLabels[$contract->payment_plan] ?? $contract->payment_plan;

        // 7. Location String
        $locationString = trim(($property?->city ?? '—') . ' - ' . ($property?->neighborhood ?? '—'));

        // 8. Contract Status Label
        $contractStatusLabel = __('contract.statuses.' . $contract->status) ?? $contract->status;
    @endphp

    {{-- Breadcrumb and back button --}}
    <div class="mb-3">
        <a href="{{ url()->previous(route('contracts.index')) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-2"></i>
            رجوع
        </a>
    </div>

    {{-- Main card container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header: Contract status + terminate button --}}
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-2">
                    <i class="fas fa-file-contract me-2 text-primary"></i>
                    تفاصيل العقد
                </h3>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-{{ $contractStatusBadge }} fs-6">
                        {{ $contractStatusLabel }}
                    </span>
                    <span class="text-muted small">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $beginDate->format('Y-m-d') }} إلى {{ $endDate->format('Y-m-d') }}
                    </span>
                </div>
            </div>

            {{-- Terminate button --}}
            @if(in_array($contract->status, ['active', 'pending']))
                <div>
                    <form action="{{ route('contracts.destroy', $contract) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من فسخ هذا العقد؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            فسخ العقد
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Financial Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-money-bill-wave me-1"></i> المبلغ الإجمالي
                    </div>
                    <div class="fw-bold fs-4 text-primary">
                        {{ number_format($totalAmount, 2) }} ر.س
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-check-circle me-1"></i> المدفوع
                    </div>
                    <div class="fw-bold fs-4 text-success">
                        {{ number_format($paidAmount, 2) }} ر.س
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-hourglass-half me-1"></i> المتبقي
                    </div>
                    <div class="fw-bold fs-4 text-warning">
                        {{ number_format($remainingAmount, 2) }} ر.س
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content: Contract Details + Tenant + Unit --}}
        <div class="row g-4 mb-4">

            {{-- Contract Details Card --}}
            <div class="col-lg-4">
                <div class="border rounded-3 p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-file-contract me-2 text-primary"></i>
                        معلومات العقد
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">تاريخ البداية</div>
                            <div class="fw-semibold">{{ $beginDate->format('Y-m-d') }}</div>
                            <div class="text-muted small">{{ $beginDate->format('M Y') }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">تاريخ النهاية</div>
                            <div class="fw-semibold">{{ $endDate->format('Y-m-d') }}</div>
                            <div class="text-muted small">{{ $endDate->format('M Y') }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">مدة العقد</div>
                            <div class="fw-semibold">
                                <span class="badge bg-light text-dark">
                                    {{ $durationMonths }} شهر ({{ $durationDays }} يوم)
                                </span>
                            </div>
                        </div>

                        {{-- Days Remaining / Expired --}}
                        @if($contract->status === 'active')
                            <div class="col-md-6">
                                <div class="text-muted small">الأيام المتبقية</div>
                                @if($daysRemaining > 0)
                                    <div class="fw-semibold">
                                        {{ $daysRemaining }} يوم
                                    </div>
                                @elseif($daysRemaining <= 0)
                                    <div class="fw-semibold text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        منتهي منذ {{ abs($daysRemaining) }} يوم
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="col-md-6">
                            <div class="text-muted small">خطة الدفع</div>
                            <div class="fw-semibold text-capitalize">{{ $paymentPlanLabel }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">عدد الدفعات المتوقعة</div>
                            <div class="fw-semibold">{{ $totalPayments }} دفعة</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">الحالة</div>
                            <div>
                                <span class="badge bg-{{ $contractStatusBadge }} fs-6">
                                    {{ $contractStatusLabel }}
                                </span>
                            </div>
                        </div>

                        @if($contract->ended_at)
                        <div class="col-md-6">
                            <div class="text-muted small">تاريخ الإنهاء</div>
                            <div class="fw-semibold text-danger">
                                {{ \Carbon\Carbon::parse($contract->ended_at)->format('Y-m-d') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tenant Details Card --}}
            <div class="col-lg-4">
                <div class="border rounded-3 p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-user me-2 text-primary"></i>
                        بيانات المستأجر
                    </h5>

                    @if($tenant)
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-muted small">الاسم الكامل</div>
                                <div class="fw-semibold fs-5">
                                    {{ $tenant->first_name }} {{ $tenant->last_name }}
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="text-muted small">رقم الهوية</div>
                                <div class="fw-semibold">
                                    {{ $tenant->national_id ?? '—' }}
                                </div>
                            </div>

                            
                            <div class="col-md-6">
                                <div class="text-muted small">رقم الهاتف</div>
                                <div class="fw-semibold">
                                    <i class="fas fa-phone me-1 text-success"></i>
                                    <a href="tel:{{ $tenant->phone_number }}" class="text-decoration-none">
                                        {{ $tenant->phone_number ?? '—' }}
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">الجنسية</div>
                                <div class="fw-semibold">
                                    {{ $tenant->nationality ?? '—' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">البريد الإلكتروني</div>
                                <div class="fw-semibold">
                                    <i class="fas fa-envelope me-1 text-info"></i>
                                    @if($tenant->email)
                                        <a href="mailto:{{ $tenant->email }}" class="text-decoration-none">
                                            {{ $tenant->email }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>لا توجد معلومات المستأجر</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Unit Details Card --}}
            <div class="col-lg-4">
                <div class="border rounded-3 p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-door-open me-2 text-primary"></i>
                        بيانات الوحدة
                    </h5>

                    @if($unit)
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-muted small">اسم الوحدة</div>
                                <div class="fw-semibold fs-5">{{ $unit->name ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">النوع</div>
                                <div class="fw-semibold">
                                    {{ __('unit.types.' . $unit->type) ?? '—' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">المساحة</div>
                                <div class="fw-semibold">
                                    {{ is_numeric($unit->area) ? number_format($unit->area) . ' م²' : '—' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">الحالة</div>
                                <div>
                                    @php
                                        $unitStatusBadges = [
                                            'available'         => 'success',
                                            'rented'            => 'warning',
                                            'sold'              => 'secondary',
                                            'under_maintenance' => 'danger',
                                        ];
                                        $unitStatusBadge = $unitStatusBadges[$unit->status] ?? 'info';
                                    @endphp
                                    <span class="badge bg-{{ $unitStatusBadge }}">
                                        {{ __('unit.statuses.' . $unit->status) ?? '—' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">
                                    <i class="fas fa-building me-1"></i> العقار
                                </div>
                                <div class="fw-semibold">{{ $asset?->name ?? '—' }}</div>
                                <div class="text-muted small">{{ $locationString }}</div>
                            </div>

                            <div class="col-12 mt-3 pt-3 border-top">
                                <a href="{{ route('units.show', $unit) }}"
                                   class="btn btn-primary btn-sm w-100">
                                    <i class="fas  me-1"></i> تفاصيل الوحدة
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-door-closed fa-2x mb-2"></i>
                            <p>لا توجد معلومات الوحدة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payments Section --}}
        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-receipt me-2 text-primary"></i>
                سجل الدفعات
            </h5>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="badge bg-light text-dark">الإجمالي: {{ $totalPayments }}</span>
                <span class="badge bg-success">مدفوعة: {{ $paidPayments }}</span>
                <span class="badge bg-warning">قيد الانتظار: {{ $pendingPayments }}</span>
                @if($overduePayments > 0)
                    <span class="badge bg-danger">متأخرة: {{ $overduePayments }}</span>
                @endif
                @if($cancelledPayments > 0)
                    <span class="badge bg-secondary">ملغاة: {{ $cancelledPayments }}</span>
                @endif
            </div>
        </div>

        @if($payments->isEmpty())
            <div class="alert alert-info mb-0">
                لا توجد دفعات مرتبطة بهذا العقد حالياً.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الدفعة</th>
                            <th>تاريخ الاستحقاق</th>
                            <th>المبلغ المطلوب</th>
                            <th>تاريخ السداد</th>
                            <th>المبلغ المدفوع</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            @php
                                $statusBadge = $paymentStatusBadges[$payment->status] ?? 'secondary';
                                $statusLabel = $paymentStatusLabels[$payment->status] ?? $payment->status;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        #{{ $payment->payment_number }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($payment->due_date)->format('Y-m-d') }}</div>
                                    <div class="text-muted small">
                                        {{ \Carbon\Carbon::parse($payment->due_date)->format('M Y') }}
                                    </div>
                                </td>
                                <td class="fw-semibold">
                                    {{ number_format($payment->amount, 2) }} ر.س
                                </td>
                                <td>
                                    @if($payment->paid_date)
                                        <div>{{ \Carbon\Carbon::parse($payment->paid_date)->format('Y-m-d') }}</div>
                                        <div class="text-muted small">
                                            {{ \Carbon\Carbon::parse($payment->paid_date)->format('M Y') }}
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-semibold text-success">
                                    {{ $payment->paid_amount ? number_format($payment->paid_amount, 2) . ' ر.س' : '—' }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusBadge }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="fw-bold">الإجمالي</td>
                            <td class="fw-bold">
                                {{ number_format($totalAmount, 2) }} ر.س
                            </td>
                            <td></td>
                            <td class="fw-bold">
                                {{ number_format($paidAmount, 2) }} ر.س
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
@endsection