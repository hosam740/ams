@extends('layouts.app')
@section('title','قائمة العقود')

@section('content')
    {{-- Global errors --}}
    @include('components.global-errors')

    {{-- Page container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header + primary action --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-file-contract me-2 text-primary"></i> قائمة العقود
            </h3>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة عقد
            </a>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Empty state --}}
        @if($contracts->isEmpty())
            <div class="alert alert-info mb-0">لا توجد عقود حالياً.</div>
        @else
            {{-- Data table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المستأجر</th>
                            <th>العمارة</th>
                            <th>الوحدة</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th>المبلغ الإجمالي</th>
                            <th>خطة الدفع</th>
                            <th>عدد الدفعات</th>
                            <th>الحالة</th>
                            <th class="text-center" style="width: 220px;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $i => $contract)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                {{-- Tenant --}}
                                <td>
                                    {{ $contract->tenant->first_name ?? '' }}
                                    {{ $contract->tenant->last_name ?? '' }}
                                </td>

                                {{-- Building name --}}
                                <td>
                                    {{ $contract->unit->property?->asset?->name ?? '—' }}
                                </td>

                                {{-- Unit --}}
                                <td>
                                    {{ $contract->unit->name ?? '—' }}
                                    <div class="small text-muted">
                                        {{ ucfirst($contract->unit->type ?? '') }}
                                        — {{ $contract->unit->area ?? '' }} م²
                                    </div>
                                </td>
                                
                                {{-- Dates --}}
                                <td>{{ $contract->beginning_date }}</td>
                                <td>{{ $contract->end_date }}</td>

                                {{-- Total amount --}}
                                <td>{{ number_format($contract->total_amount, 2) }} SAR</td>

                                {{-- Payment plan --}}
                                <td>{{ ucfirst(str_replace('_',' ', $contract->payment_plan)) }}</td>

                                {{-- Number of payments --}}
                                <td>
                                    @php
                                        $plan = $contract->payment_plan;
                                        $numPayments = match($plan) {
                                            'monthly'      => 12,
                                            'quarterly'    => 4,
                                            'semi_annual'  => 2,
                                            'annual'       => 1,
                                            default        => 1,
                                        };
                                    @endphp
                                    {{ $numPayments }}
                                </td>

                                {{-- Status --}}
                                <td>
                                    @php
                                        if (isset($contract->active)) {
                                            $statusKey = $contract->active ? 'active' : 'inactive';
                                        } else {
                                            $today = \Carbon\Carbon::today();
                                            $begin = \Carbon\Carbon::parse($contract->beginning_date);
                                            $end   = \Carbon\Carbon::parse($contract->end_date);
                                            $endedAt = $contract->ended_at ? \Carbon\Carbon::parse($contract->ended_at) : null;

                                            if ($endedAt && $endedAt->lte($today)) {
                                                $statusKey = 'ended';
                                            } elseif ($today->lt($begin)) {
                                                $statusKey = 'not_started';
                                            } elseif ($today->between($begin, $end)) {
                                                $statusKey = 'active';
                                            } else {
                                                $statusKey = 'ended';
                                            }
                                        }

                                        $labels = [
                                            'active'      => 'ساري',
                                            'inactive'    => 'متوقف',
                                            'not_started' => 'لم يبدأ',
                                            'ended'       => 'منتهي',
                                        ];

                                        $badges = [
                                            'active'      => 'success',
                                            'inactive'    => 'secondary',
                                            'not_started' => 'warning',
                                            'ended'       => 'danger',
                                        ];

                                        $label = $labels[$statusKey] ?? '—';
                                        $badge = $badges[$statusKey] ?? 'secondary';
                                    @endphp

                                    <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                                </td>

                                {{-- Actions --}}
                                <td class="text-center">
                                    {{-- View --}}
                                    <a href="{{ route('contracts.show', $contract) }}"
                                       class="btn btn-sm btn-outline-info me-1" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('contracts.edit', $contract) }}"
                                       class="btn btn-sm btn-outline-primary me-1" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('contracts.destroy', $contract) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا العقد؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
