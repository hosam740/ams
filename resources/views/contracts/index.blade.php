@extends('layouts.app')

@section('title', 'قائمة العقود')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">العقود</h4>
    <div>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> إضافة عقد
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($contracts->isEmpty())
    <div class="alert alert-info">لا توجد عقود حالياً.</div>
@else
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>المستأجر</th>
                <th>الوحدة</th>
                <th>تاريخ البداية</th>
                <th>تاريخ النهاية</th>
                <th>المبلغ الإجمالي</th>
                <th>خطة الدفع</th>
                <th>عدد الدفعات</th>
                <th class="text-center" style="width: 200px;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $i => $contract)
                <tr>
                    <td>{{ $i + 1 }}</td>

                    {{-- المستأجر --}}
                    <td>
                        {{ $contract->tenant->first_name ?? '' }}
                        {{ $contract->tenant->last_name ?? '' }}
                    </td>

                    {{-- الوحدة --}}
                    <td>
                        {{ $contract->unit->name ?? '—' }}
                        <div class="small text-muted">
                            {{ ucfirst($contract->unit->type ?? '') }}
                            — {{ $contract->unit->area ?? '' }} م²
                        </div>
                    </td>

                    {{-- اسم العمارة واسم المالك --}}
                        @if($contract->unit->property?->asset)
                            <div class="small text-muted">
                                العمارة: {{ $contract->unit->property->asset->name }}
                            </div>
                            <div class="small text-muted">
                                المالك: {{ $contract->unit->property->asset->manager->name ?? '—' }}
                            </div>
                        @endif
                    </td>

                    <td>{{ $contract->beginning_date }}</td>
                    <td>{{ $contract->end_date }}</td>

                    <td>{{ number_format($contract->total_amount, 2) }} SAR</td>

                    {{-- خطة الدفع --}}
                    <td>{{ ucfirst(str_replace('_',' ', $contract->payment_plan)) }}</td>

                    {{-- عدد الدفعات --}}
                    <td>
                        @php
                            $plan = $contract->payment_plan;
                            $numPayments = match($plan) {
                                'monthly'   => 12,
                                'quarterly' => 4,
                                'semi_annual' => 2,
                                'annual'    => 1,
                                default     => 1,
                            };
                        @endphp
                        {{ $numPayments }}
                    </td>

                    {{-- حالة العقد --}}
                    <td>
                        @php
                        // لو عندك عمود active، استخدمه أولاً
                        if (isset($contract->active)) {
                            $statusKey = $contract->active ? 'active' : 'inactive';
                        } else {
                            // استنتاج من التواريخ
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
                            'active'       => 'ساري',
                            'inactive'     => 'متوقف',
                            'not_started'  => 'لم يبدأ',
                            'ended'        => 'منتهي',
                        ];

                        $badges = [
                            'active'       => 'success',
                            'inactive'     => 'secondary',
                            'not_started'  => 'warning',
                            'ended'        => 'danger',
                        ];

                        $label = $labels[$statusKey] ?? '—';
                        $badge = $badges[$statusKey] ?? 'secondary';
                        @endphp

                        <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                    </td>


                    <td class="text-center">
                        {{-- عرض --}}
                        <a href="{{ route('contracts.show', $contract) }}"
                           class="btn btn-outline-secondary btn-sm me-1" title="عرض">
                            <i class="bi bi-eye"></i>
                        </a>

                        {{-- تعديل --}}
                        <a href="{{ route('contracts.edit', $contract) }}"
                           class="btn btn-outline-primary btn-sm me-1" title="تعديل">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        {{-- حذف --}}
                        <form action="{{ route('contracts.destroy', $contract) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العقد؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="حذف">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
