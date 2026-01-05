@extends('layouts.app')
@section('title','عرض العقار')

@section('content')
    @include('components.global-errors')

    @php
        $assetName = optional($property->asset)->name ?? '—';
        $units = $property->units ?? collect();
        $counts = [
            'total' => $units->count(),
            'available' => $units->where('status', 'available')->count(),
            'rented' => $units->where('status', 'rented')->count(),
            'sold' => $units->where('status', 'sold')->count(),
            'under_maintenance' => $units->where('status', 'under_maintenance')->count(),
        ];
        $locationLine = trim(($property->city ?? '—') . ' - ' . ($property->neighborhood ?? '—'));
    @endphp

    <div class="bg-white shadow-1 round-lg p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="fas fa-building me-2 text-primary"></i> {{ $assetName }}
                </h3>
                <div class="text-muted small">
                    {{ $property->country ?? '—' }} • {{ $locationLine }}
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('properties.edit', $property) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> تعديل
                </a>
                <form action="{{ route('properties.destroy', $property) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف العقار؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-1"></i> حذف
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="border rounded-3 p-3 h-100">
                    <h5 class="fw-bold mb-3">بيانات العقار</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">الدولة</div>
                            <div class="fw-semibold">{{ $property->country ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">المدينة</div>
                            <div class="fw-semibold">{{ $property->city ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">الحي</div>
                            <div class="fw-semibold">{{ $property->neighborhood ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">المساحة</div>
                            <div class="fw-semibold">
                                {{ is_numeric($property->area ?? null) ? number_format($property->area) . ' م²' : '—' }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">رابط الموقع</div>
                            @if(!empty($property->url_location))
                                <a href="{{ $property->url_location }}"
                                   target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="fa-solid fa-location-dot"></i> فتح الخريطة
                                </a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="border rounded-3 p-3 h-100">
                    <h5 class="fw-bold mb-3">إحصائيات الوحدات</h5>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">إجمالي الوحدات</div>
                                <div class="fs-5 fw-bold">{{ $counts['total'] }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">المتاحة</div>
                                <div class="fs-5 fw-bold text-success">{{ $counts['available'] }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">المؤجرة</div>
                                <div class="fs-5 fw-bold text-warning">{{ $counts['rented'] }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">المباعة</div>
                                <div class="fs-5 fw-bold text-secondary">{{ $counts['sold'] }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small">تحت الصيانة</div>
                                <div class="fs-5 fw-bold text-danger">{{ $counts['under_maintenance'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-door-open me-2 text-primary"></i> الوحدات المرتبطة
            </h5>
            <a href="{{ route('units.create', $property) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> إضافة وحدة
            </a>
        </div>

        @if($units->isEmpty())
            <div class="alert alert-info mb-0">لا توجد وحدات مرتبطة بهذا العقار حالياً.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الوحدة</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th class="text-nowrap">المساحة</th>
                            <th class="text-center" style="width: 180px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $i => $unit)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $unit->name ?? '—' }}</td>
                                <td class="text-capitalize">
                                    {{ $unit->type ? __('unit.types.' . $unit->type) : '—' }}
                                </td>
                                <td>
                                    @php
                                        $status = $unit->status ?? 'unknown';
                                        $statusMap = [
                                            'available' => 'success',
                                            'rented' => 'warning',
                                            'sold' => 'secondary',
                                            'under_maintenance' => 'danger',
                                        ];
                                        $badge = $statusMap[$status] ?? 'info';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">
                                        {{ $unit->status ? __('unit.statuses.' . $status) : '—' }}
                                    </span>
                                </td>
                                <td>
                                    {{ is_numeric($unit->area ?? null) ? number_format($unit->area) . ' م²' : '—' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('units.show', $unit) }}"
                                       class="btn btn-sm btn-outline-info me-1" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('units.edit', $unit) }}"
                                       class="btn btn-sm btn-outline-primary me-1" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('units.destroy', $unit) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟');">
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
