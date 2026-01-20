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
                                            'due'       => 'مستحقة',
                                            'paid'      => 'مدفوعة',
                                            'overdue'   => 'متأخرة',
                                            'cancelled' => 'ملغاة',
                                        ];
                                        $badges = [
                                            'pending'   => 'warning',
                                            'due'       => 'info',
                                            'paid'      => 'success',
                                            'overdue'   => 'danger',
                                            'cancelled' => 'secondary',
                                        ];
                                        $label = $labels[$statusKey] ?? $statusKey;
                                        $badge = $badges[$statusKey] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                                </td>

                                {{-- Actions --}}
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('payments.show', $payment) }}"
                                           class="btn btn-sm btn-outline-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($payment->canBeMarkedAsPaid())
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#markPaidModal{{ $payment->id }}"
                                                    title="تسجيل السداد">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        @if($payment->canBeCancelled())
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cancelModal{{ $payment->id }}"
                                                    title="إلغاء">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Modals --}}
    @foreach($payments as $payment)
        {{-- Mark as Paid Modal --}}
        @if($payment->canBeMarkedAsPaid())
            <div class="modal fade" id="markPaidModal{{ $payment->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('payments.mark-paid', $payment) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header">
                                <h5 class="modal-title">تسجيل سداد الدفعة #{{ $payment->payment_number }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">المبلغ المستحق</label>
                                    <input type="text" class="form-control" value="{{ number_format($payment->amount, 2) }} SAR" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="paid_amount{{ $payment->id }}" class="form-label">المبلغ المدفوع <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="paid_amount{{ $payment->id }}"
                                           name="paid_amount" value="{{ $payment->amount }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="paid_date{{ $payment->id }}" class="form-label">تاريخ السداد <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="paid_date{{ $payment->id }}"
                                           name="paid_date" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="payment_method{{ $payment->id }}" class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                                    <select class="form-select" id="payment_method{{ $payment->id }}" name="payment_method" required>
                                        <option value="">اختر...</option>
                                        @foreach(\App\Models\Payment::getPaymentMethods() as $method)
                                            <option value="{{ $method }}">
                                                @switch($method)
                                                    @case('cash') نقداً @break
                                                    @case('bank_transfer') تحويل بنكي @break
                                                    @case('check') شيك @break
                                                    @default {{ $method }}
                                                @endswitch
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="notes{{ $payment->id }}" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="notes{{ $payment->id }}" name="notes" rows="2">{{ $payment->notes }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-success">تسجيل السداد</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Cancel Modal --}}
        @if($payment->canBeCancelled())
            <div class="modal fade" id="cancelModal{{ $payment->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('payments.cancel', $payment) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header">
                                <h5 class="modal-title">إلغاء الدفعة #{{ $payment->payment_number }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-danger">هل أنت متأكد من إلغاء هذه الدفعة؟</p>
                                <div class="mb-3">
                                    <label for="cancel_notes{{ $payment->id }}" class="form-label">سبب الإلغاء</label>
                                    <textarea class="form-control" id="cancel_notes{{ $payment->id }}" name="notes" rows="2">{{ $payment->notes }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
