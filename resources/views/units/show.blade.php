@extends('layouts.app')
@section('title','عرض الوحدة')

@section('content')
    @include('components.global-errors')

    @php
        // Extract related data
        $property = $unit->property;
        $asset = $property?->asset;
        $tenant = $primaryContract?->tenant;

        // Status badge mapping
        $statusMap = [
            'available'         => 'success',
            'rented'            => 'warning',
            'sold'              => 'secondary',
            'under_maintenance' => 'danger',
        ];
        $statusBadge = $statusMap[$unit->status] ?? 'info';

        // Breadcrumb data
        $assetName = $asset?->name ?? 'عقار غير محدد';
        $propertyLocation = trim(($property?->city ?? '—') . ' - ' . ($property?->neighborhood ?? '—'));
    @endphp

    {{-- Breadcrumb and back button --}}
    <div class="mb-3">
        <a href="{{ route('properties.show', $property) }}" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-right me-1"></i>
            العودة إلى {{ $assetName }}
        </a>
    </div>

    {{-- Main card container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header: Unit name + status badge + actions --}}
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-2">
                    <i class="fas fa-door-open me-2 text-primary"></i>
                    {{ $unit->name ?? 'وحدة بدون اسم' }}
                </h3>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-{{ $statusBadge }} fs-6">
                        {{ __('unit.statuses.' . $unit->status) }}
                    </span>
                    <span class="text-muted small">
                        <i class="fas fa-building me-1"></i>
                        {{ $assetName }} • {{ $propertyLocation }}
                    </span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('units.edit', $unit) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> تعديل
                </a>
                <form action="{{ route('units.destroy', $unit) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-1"></i> حذف
                    </button>
                </form>
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

        {{-- Stats row: Quick info cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-tag me-1"></i> النوع
                    </div>
                    <div class="fw-bold fs-5">
                        {{ __('unit.types.' . $unit->type) }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-ruler-combined me-1"></i> المساحة
                    </div>
                    <div class="fw-bold fs-5">
                        {{ is_numeric($unit->area) ? number_format($unit->area) . ' م²' : '—' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-info-circle me-1"></i> الحالة
                    </div>
                    <div class="fw-bold fs-5">
                        <span class="badge bg-{{ $statusBadge }}">
                            {{ __('unit.statuses.' . $unit->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-1">
                        <i class="fas fa-calendar me-1"></i> آخر تحديث
                    </div>
                    <div class="fw-bold fs-6">
                        {{ $unit->updated_at?->diffForHumans() ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content: Unit details + Contract --}}
        <div class="row g-4">
            {{-- Left column: Unit details --}}
            <div class="col-lg-6">
                <div class="border rounded-3 p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        معلومات الوحدة
                    </h5>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="text-muted small">اسم الوحدة</div>
                            <div class="fw-semibold">{{ $unit->name ?? '—' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">النوع</div>
                            <div class="fw-semibold">{{ __('unit.types.' . $unit->type) }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">المساحة</div>
                            <div class="fw-semibold">
                                {{ is_numeric($unit->area) ? number_format($unit->area) . ' م²' : '—' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted small">الحالة</div>
                            <div>
                                <span class="badge bg-{{ $statusBadge }}">
                                    {{ __('unit.statuses.' . $unit->status) }}
                                </span>
                            </div>
                        </div>

                        @if($unit->description)
                        <div class="col-12">
                            <div class="text-muted small">الوصف</div>
                            <div class="fw-semibold">{{ $unit->description }}</div>
                        </div>
                        @endif

                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="text-muted small mb-2">
                                <i class="fas fa-building me-1"></i> العقار التابع له
                            </div>
                            <div class="fw-semibold mb-1">{{ $assetName }}</div>
                            <div class="text-muted small">{{ $propertyLocation }}</div>
                            @if($property?->url_location)
                                <a href="{{ $property->url_location }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn btn-sm btn-outline-secondary mt-2">
                                    <i class="fas fa-location-dot me-1"></i> عرض على الخريطة
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column: Contract details --}}
            <div class="col-lg-6">
                <div class="border rounded-3 p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-file-contract me-2 text-primary"></i>
                        العقد الحالي
                    </h5>

                    @if($primaryContract)
                        {{-- Contract exists --}}
                        @php
                            $contractStatusKey = $primaryContract->status;
                            $contractStatusLabel = __('contract.statuses.' . $contractStatusKey) ?? $contractStatusKey;
                            $contractStatusBadge = __('contract.badges.' . $contractStatusKey) ?? 'secondary';

                            // Calculate days remaining
                            $endDate = \Carbon\Carbon::parse($primaryContract->end_date);
                            $today = \Carbon\Carbon::today();
                            $daysRemaining = $today->diffInDays($endDate, false);
                        @endphp

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="text-muted small">المستأجر</div>
                                <div class="fw-semibold">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $tenant?->first_name }} {{ $tenant?->last_name }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">تاريخ البداية</div>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($primaryContract->beginning_date)->format('Y-m-d') }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">تاريخ النهاية</div>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($primaryContract->end_date)->format('Y-m-d') }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">المبلغ الإجمالي</div>
                                <div class="fw-bold text-success">
                                    {{ number_format($primaryContract->total_amount, 2) }} ر.س
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">خطة الدفع</div>
                                <div class="fw-semibold text-capitalize">
                                    {{ str_replace('_', ' ', $primaryContract->payment_plan) }}
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-muted small">الحالة</div>
                                <div>
                                    <span class="badge bg-{{ $contractStatusBadge }} fs-6">
                                        {{ $contractStatusLabel }}
                                    </span>
                                </div>
                            </div>

                            @if($daysRemaining > 0 && $primaryContract->status === 'active')
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    متبقي <strong>{{ $daysRemaining }}</strong> يوم على انتهاء العقد
                                </div>
                            </div>
                            @elseif($daysRemaining <= 0 && $primaryContract->status === 'active')
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    العقد منتهي منذ <strong>{{ abs($daysRemaining) }}</strong> يوم
                                </div>
                            </div>
                            @endif

                            <div class="col-12 mt-3 pt-3 border-top">
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('contracts.show', $primaryContract) }}"
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye me-1"></i> عرض العقد
                                    </a>
                                    <a href="{{ route('contracts.edit', $primaryContract) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit me-1"></i> تعديل العقد
                                    </a>
                                    <a href="{{ route('payments.index') }}"
                                       class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-money-bill-wave me-1"></i> الدفعات
                                    </a>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- No contract exists --}}
                        <div class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-file-circle-xmark fa-3x"></i>
                            </div>
                            <p class="text-muted mb-3">لا يوجد عقد مرتبط بهذه الوحدة حالياً</p>

                            @if($unit->status === 'available')
                                <a href="{{ route('contracts.create', ['unit_id' => $unit->id]) }}"
                                   class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> إنشاء عقد جديد
                                </a>
                            @else
                                <div class="alert alert-warning d-inline-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    الوحدة غير متاحة حالياً لإنشاء عقد
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contract History Section --}}
        @if($otherContracts->isNotEmpty())
            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>
                    سجل العقود السابقة
                </h5>
                <span class="badge bg-secondary">{{ $otherContracts->count() }} عقد</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المستأجر</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th>المدة</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th class="text-center" style="width: 150px;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otherContracts as $index => $contract)
                            @php
                                $statusKey = $contract->status;
                                $statusLabel = __('contract.statuses.' . $statusKey) ?? $statusKey;
                                $statusBadgeColor = __('contract.badges.' . $statusKey) ?? 'secondary';

                                $beginDate = \Carbon\Carbon::parse($contract->beginning_date);
                                $endDate = \Carbon\Carbon::parse($contract->end_date);
                                $duration = $beginDate->diffInMonths($endDate);
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $contract->tenant?->first_name }} {{ $contract->tenant?->last_name }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $contract->tenant?->phone ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-nowrap">{{ $beginDate->format('Y-m-d') }}</div>
                                    <div class="text-muted small">{{ $beginDate->format('M Y') }}</div>
                                </td>
                                <td>
                                    <div class="text-nowrap">{{ $endDate->format('Y-m-d') }}</div>
                                    <div class="text-muted small">{{ $endDate->format('M Y') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $duration }} شهر
                                    </span>
                                </td>
                                <td class="fw-bold text-success">
                                    {{ number_format($contract->total_amount, 2) }} ر.س
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusBadgeColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('contracts.show', $contract) }}"
                                       class="btn btn-sm btn-outline-info me-1"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('contracts.edit', $contract) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
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
