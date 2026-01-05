@extends('layouts.app')
@section('title','قائمة الدفعات')

@section('content')
    {{-- Global errors --}}
    @include('components.global-errors')

    <div class="bg-white shadow-1 round-lg p-4">
        {{-- Header + primary action --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-receipt me-2 text-primary"></i> قائمة الدفعات
            </h3>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Empty state --}}
        @if($payments->isEmpty())
            <div class="alert alert-info mb-0">لا توجد دفعات حالياً.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العقد</th>
                            <th>المستأجر</th>
                            <th>الوحدة</th>
                            <th>رقم الدفعة</th>
                            <th>تاريخ الاستحقاق</th>
                            <th>المبلغ</th>
                            <th>تاريخ السداد</th>
                            <th>المبلغ المدفوع</th>
                            <th>الحالة</th>
                            <th class="text-center" style="width: 120px;">عرض</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $i => $payment)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                {{-- Contract --}}
                                <td>
                                    <a href="{{ route('contracts.show', $payment->contract_id) }}" class="text-decoration-none">
                                        #{{ $payment->contract_id }}
                                    </a>
                                </td>

                                {{-- Tenant --}}
                                <td>
                                    {{ $payment->contract?->tenant?->first_name ?? '' }}
                                    {{ $payment->contract?->tenant?->last_name ?? '' }}
                                </td>

                                {{-- Unit --}}
                                <td>
                                    {{ $payment->contract?->unit?->name ?? '—' }}
                                    <div class="small text-muted">
                                        {{ $payment->contract?->unit?->property?->asset?->name ?? '' }}
                                    </div>
                                </td>

                                {{-- Payment number --}}
                                <td>{{ $payment->payment_number ?? '—' }}</td>

                                {{-- Due date --}}
                                <td>{{ optional($payment->due_date)->format('Y-m-d') }}</td>

                                {{-- Amount --}}
                                <td>{{ number_format($payment->amount, 2) }} SAR</td>

                                {{-- Paid date --}}
                                <td>{{ $payment->paid_date ? \Carbon\Carbon::parse($payment->paid_date)->format('Y-m-d') : '—' }}</td>

                                {{-- Paid amount --}}
                                <td>{{ $payment->paid_amount ? number_format($payment->paid_amount, 2) . ' SAR' : '—' }}</td>

                                {{-- Status --}}
                                <td>
                                    @php
                                        $statusKey = $payment->status;
                                        $labels = [
                                            'pending'   => 'قيد الانتظار',
                                            'paid'      => 'مدفوعة',
                                            'overdue'   => 'متأخرة',
                                            'cancelled' => 'ملغاة',
                                        ];
                                        $badges = [
                                            'pending'   => 'warning',
                                            'paid'      => 'success',
                                            'overdue'   => 'danger',
                                            'cancelled' => 'secondary',
                                        ];
                                        $label = $labels[$statusKey] ?? $statusKey;
                                        $badge = $badges[$statusKey] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                                </td>

                                {{-- View only --}}
                                <td class="text-center">
                                    <a href="{{ route('payments.show', $payment) }}"
                                       class="btn btn-sm btn-outline-info" title="عرض">
                                        <i class="fas fa-eye"></i>
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
