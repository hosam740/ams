@extends('layouts.app')
@section('title','عرض المستأجر')

@section('content')
    @include('components.global-errors')

    @php
        // ============================================
        // Data Preparation Section
        // All relationships are eager-loaded in controller
        // No additional queries will be executed here
        // ============================================

        // 1. Extract Related Models (already loaded)
        $contracts = $tenant->contracts; // Already loaded & sorted

        // 2. Status Badges Mapping
        $contractStatusBadges = [
            'active'     => 'success',
            'pending'    => 'warning',
            'expired'    => 'secondary',
            'terminated' => 'danger',
        ];

        $contractStatusLabels = [
            'active'     => 'نشط',
            'pending'    => 'قيد الانتظار',
            'expired'    => 'منتهي',
            'terminated' => 'مفسوخ',
        ];

        // 3. Contract Statistics (using collection methods, no queries)
        $totalContracts = $contracts->count();
        $activeContracts = $contracts->where('status', 'active')->count();
        $pendingContracts = $contracts->where('status', 'pending')->count();
        $expiredContracts = $contracts->where('status', 'expired')->count();
        $terminatedContracts = $contracts->where('status', 'terminated')->count();

        // 4. Tenant Statistics
        // Count only units with active or pending contracts
        $uniqueUnits = $contracts
            ->whereIn('status', ['active', 'pending'])
            ->pluck('unit.id')
            ->unique()
            ->count();

        // 5. Sort contracts: active first, then pending, then others by date
        // Exclude primary contract from the list
        $sortedContracts = $contracts
            ->reject(function($contract) use ($primaryContract) {
                return $primaryContract && $contract->id === $primaryContract->id;
            })
            ->sortBy([
                fn($a, $b) => $a->status === 'active' ? -1 : ($b->status === 'active' ? 1 : 0),
                fn($a, $b) => $a->status === 'pending' ? -1 : ($b->status === 'pending' ? 1 : 0),
                fn($a, $b) => strcmp($b->beginning_date, $a->beginning_date)
            ]);

        // 6. Primary contract details (if exists)
        if ($primaryContract) {
            $primaryUnit = $primaryContract->unit;
            $primaryProperty = $primaryUnit?->property;
            $primaryAsset = $primaryProperty?->asset;
            $primaryPayments = $primaryContract->payments;

            $primaryBeginDate = \Carbon\Carbon::parse($primaryContract->beginning_date);
            $primaryEndDate = \Carbon\Carbon::parse($primaryContract->end_date);
            $primaryToday = \Carbon\Carbon::today();

            $primaryDurationMonths = $primaryBeginDate->diffInMonths($primaryEndDate);
            $primaryDaysRemaining = $primaryToday->diffInDays($primaryEndDate, false);

            $primaryPaidAmount = $primaryPayments->where('status', 'paid')->sum('paid_amount');
            $primaryRemainingAmount = $primaryContract->total_amount - $primaryPaidAmount;

            $primaryLocationString = trim(($primaryProperty?->city ?? '—') . ' - ' . ($primaryProperty?->neighborhood ?? '—'));

            $primaryStatusBadge = $contractStatusBadges[$primaryContract->status] ?? 'secondary';
            $primaryStatusLabel = $contractStatusLabels[$primaryContract->status] ?? $primaryContract->status;
        }
    @endphp

    {{-- Smart back button with fallback --}}
    <div class="mb-3">
        <a href="{{ url()->previous(route('tenants.index')) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-2"></i>
            رجوع
        </a>
    </div>

    {{-- Main card container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header: Tenant name --}}
        <div class="mb-4">
            <h3 class="fw-bold mb-2">
                <i class="fas fa-user me-2 text-primary"></i>
                تفاصيل المستأجر
            </h3>
            <div class="text-muted">
                {{ $tenant->first_name }} {{ $tenant->last_name }}
            </div>
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

        {{-- Tenant Info Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-file-contract me-1"></i> عدد العقود
                    </div>
                    <div class="fw-bold fs-4">
                        {{ $totalContracts }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-check-circle me-1"></i> العقود النشطة
                    </div>
                    <div class="fw-bold fs-4 text-success">
                        {{ $activeContracts }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-door-open me-1"></i> عدد الوحدات المستأجرة
                    </div>
                    <div class="fw-bold fs-4">
                        {{ $uniqueUnits }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content: Tenant Details + Primary Contract --}}
        <div class="row g-4 mb-4">

            {{-- Tenant Information Card (Single Card) --}}
            <div class="col-lg-6">
                <div class="border rounded-3 p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-user me-2 text-primary"></i>
                        بيانات المستأجر
                    </h5>

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
                            <div class="text-muted small">الجنسية</div>
                            <div class="fw-semibold">
                                {{ $tenant->nationality ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">رقم الهاتف</div>
                            <div class="fw-semibold">
                                @if($tenant->phone_number)
                                    <a href="tel:{{ $tenant->phone_number }}" class="text-decoration-none">
                                        {{ $tenant->phone_number }}
                                    </a>
                                @else
                                    —
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">البريد الإلكتروني</div>
                            <div class="fw-semibold">
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
                </div>
            </div>

            {{-- Primary Contract Card --}}
            <div class="col-lg-6">
                <div class="border rounded-3 p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-file-contract me-2 text-primary"></i>
                        العقد الحالي
                    </h5>

                    @if($primaryContract)
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-muted small">الوحدة</div>
                                <div class="fw-semibold fs-5">{{ $primaryUnit?->name ?? '—' }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $primaryAsset?->name ?? '—' }}
                                </div>
                                <div class="text-muted small">{{ $primaryLocationString }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">تاريخ البداية</div>
                                <div class="fw-semibold">{{ $primaryBeginDate->format('Y-m-d') }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">تاريخ النهاية</div>
                                <div class="fw-semibold">{{ $primaryEndDate->format('Y-m-d') }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">مدة العقد</div>
                                <div class="fw-semibold">{{ $primaryDurationMonths }} شهر</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">الحالة</div>
                                <div>
                                    <span class="badge bg-{{ $primaryStatusBadge }}">
                                        {{ $primaryStatusLabel }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 mt-3 pt-3 border-top">
                                <a href="{{ route('contracts.show', $primaryContract) }}"
                                   class="btn btn-primary btn-sm w-100">
                                    تفاصيل العقد
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-file-contract fa-2x mb-2"></i>
                            <p>لا توجد عقود مرتبطة بهذا المستأجر</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contracts Section --}}
        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                جميع العقود
            </h5>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="badge bg-light text-dark">الإجمالي: {{ $totalContracts }}</span>
                @if($activeContracts > 0)
                    <span class="badge bg-success">نشطة: {{ $activeContracts }}</span>
                @endif
                @if($pendingContracts > 0)
                    <span class="badge bg-warning">قيد الانتظار: {{ $pendingContracts }}</span>
                @endif
                @if($expiredContracts > 0)
                    <span class="badge bg-secondary">منتهية: {{ $expiredContracts }}</span>
                @endif
                @if($terminatedContracts > 0)
                    <span class="badge bg-danger">مفسوخة: {{ $terminatedContracts }}</span>
                @endif
            </div>
        </div>

        @if($contracts->isEmpty())
            <div class="alert alert-info mb-0">
                لا توجد عقود مرتبطة بهذا المستأجر حالياً.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>الوحدة</th>
                            <th>العقار</th>
                            <th>الموقع</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th>المبلغ الإجمالي</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sortedContracts as $contract)
                            @php
                                $unit = $contract->unit;
                                $property = $unit?->property;
                                $asset = $property?->asset;

                                $statusBadge = $contractStatusBadges[$contract->status] ?? 'secondary';
                                $statusLabel = $contractStatusLabels[$contract->status] ?? $contract->status;

                                $beginDate = \Carbon\Carbon::parse($contract->beginning_date);
                                $endDate = \Carbon\Carbon::parse($contract->end_date);

                                $locationString = trim(($property?->city ?? '—') . ' - ' . ($property?->neighborhood ?? '—'));
                            @endphp
                            <tr>
                                <td colspan="7" class="p-0">
                                    <a href="{{ route('contracts.show', $contract) }}" class="text-decoration-none text-dark d-block">
                                        <table class="w-100 m-0">
                                            <tr>
                                                <td style="width: 14.28%; padding: 0.5rem;">
                                                    <div class="fw-semibold">{{ $unit?->name ?? '—' }}</div>
                                                    <div class="text-muted small">
                                                        {{ __('unit.types.' . $unit?->type) ?? '—' }}
                                                    </div>
                                                </td>
                                                <td style="width: 14.28%; padding: 0.5rem;" class="fw-semibold">
                                                    {{ $asset?->name ?? '—' }}
                                                </td>
                                                <td style="width: 14.28%; padding: 0.5rem;">
                                                    <div class="text-muted small">{{ $locationString }}</div>
                                                </td>
                                                <td style="width: 14.28%; padding: 0.5rem;">
                                                    <div>{{ $beginDate->format('Y-m-d') }}</div>
                                                    <div class="text-muted small">{{ $beginDate->format('M Y') }}</div>
                                                </td>
                                                <td style="width: 14.28%; padding: 0.5rem;">
                                                    <div>{{ $endDate->format('Y-m-d') }}</div>
                                                    <div class="text-muted small">{{ $endDate->format('M Y') }}</div>
                                                </td>
                                                <td style="width: 14.28%; padding: 0.5rem;" class="fw-semibold">
                                                    {{ number_format($contract->total_amount, 2) }} ر.س
                                                </td>
                                                <td style="width: 14.28%; padding: 0.5rem;">
                                                    <span class="badge bg-{{ $statusBadge }}">
                                                        {{ $statusLabel }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection