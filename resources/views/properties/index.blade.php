@extends('layouts.app')
@section('title','العقارات')

@section('content')
    @include('components.global-errors')

    <div class="bg-white shadow-1 round-lg p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-building me-2 text-primary"></i> قائمة العقارات
            </h3>
            <a href="{{ route('properties.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة عقار
            </a>
        </div>

        @if($properties->isEmpty())
            <div class="alert alert-info mb-0">لا توجد عقارات حالياً.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>الاسم</th>
                            <th>الدولة</th>
                            <th>المدينة</th>
                            <th>الحي</th>
                            <th>المساحة</th>
                            <th>الحالة</th>
                            <th class="text-center" style="width: 180px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                            <tr>
                                <td>{{ optional($property->asset)->name ?? '—' }}</td>
                                <td>{{ $property->country ?? '—' }}</td>
                                <td>{{ $property->city ?? '—' }}</td>
                                <td>{{ $property->neighborhood ?? '—' }}</td>
                                <td>{{ $property->area ? number_format($property->area) . ' م²' : '—' }}</td>
                                <td>
                                    @php
                                        $status = $property->status ?? 'غير محدد';
                                        $color = match($status) {
                                            'available'   => 'success',
                                            'rented'      => 'danger',
                                            'archived'    => 'secondary',
                                            'maintenance' => 'warning',
                                            default       => 'info'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ __($status) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('properties.show', $property) }}"
                                       class="btn btn-sm btn-outline-info me-1" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('properties.edit', $property) }}"
                                       class="btn btn-sm btn-outline-primary me-1" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('properties.destroy', $property) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
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
