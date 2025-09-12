@extends('layouts.app')

@section('title', 'قائمة الوحدات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">الوحدات</h4>
    <div>
        <a href="{{ route('units.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> إنشاء وحدة
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($units->isEmpty())
    <div class="alert alert-info">لا توجد وحدات حالياً.</div>
@else
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>اسم الوحدة</th>
                <th>النوع</th>
                <th>الحالة</th>
                <th class="text-nowrap">المساحة (م²)</th>
                <th>العقار</th>
                <th>الموقع</th>
                <th class="text-center" style="width: 220px;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($units as $i => $unit)
                <tr>
                    <td>{{ $i + 1 }}</td>

                    {{-- اسم الوحدة (لو ما فيه عمود name، احذف هذا العمود) --}}
                    <td>{{ $unit->name ?? '—' }}</td>

                    <td class="text-capitalize">{{ $unit->type }}</td>

                    <td>
                        @php
                            $status = $unit->status;
                            $map = [
                                'available' => 'success',
                                'rented' => 'warning',
                                'sold' => 'secondary',
                                'under_maintenance' => 'danger',
                            ];
                            $badge = $map[$status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $badge }} text-uppercase">{{ $status }}</span>
                    </td>

                    <td>{{ rtrim(rtrim(number_format($unit->area, 2, '.', ''), '0'), '.') }}</td>

                    <td>
                        {{-- اسم الأصل (Asset) المرتبط + بيانات العقار --}}
                        <div class="fw-semibold">{{ optional($unit->property?->asset)->name ?? '—' }}</div>
                        <div class="text-muted small">
                            {{ $unit->property->city ?? '—' }} - {{ $unit->property->neighborhood ?? '—' }}
                        </div>
                    </td>

                    <td>
                        @if(!empty($unit->property?->url_location))
                            <a href="{{ $unit->property->url_location }}" target="_blank" class="text-decoration-none">
                                <i class="bi bi-geo-alt"></i> خريطة
                            </a>
                        @else
                            —
                        @endif
                    </td>

                    <td class="text-center">
                        {{-- عرض --}}
                        <a href="{{ route('units.show', $unit) }}"
                           class="btn btn-outline-secondary btn-sm me-1" title="عرض">
                            <i class="bi bi-eye"></i>
                        </a>

                        {{-- تعديل --}}
                        <a href="{{ route('units.edit', $unit) }}"
                           class="btn btn-outline-primary btn-sm me-1" title="تعديل">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        {{-- حذف --}}
                        <form action="{{ route('units.destroy', $unit) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟');">
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
