@extends('layouts.app')
@section('title','الوحدات')

@section('content')
    {{-- Global, non-field-specific errors (consistent with properties page) --}}
    @include('components.global-errors')

    {{-- Page container card (white surface + shadow + rounded) --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header: title + primary action (Add Unit) --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                {{-- Use Font Awesome to match the rest of the app (same as properties page) --}}
                <i class="fas fa-door-open me-2 text-primary"></i> قائمة الوحدات
            </h3>

            {{-- Use .btn.btn-brand for consistent brand color across the app (moved to base.css) --}}
            <a href="{{ route('units.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> إنشاء وحدة
            </a>
        </div>

        {{-- Flash success message (kept minimal and consistent) --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Empty state --}}
        @if($units->isEmpty())
            <div class="alert alert-info mb-0">لا توجد وحدات حالياً.</div>
        @else
            {{-- Responsive table wrapper --}}
            <div class="table-responsive">
                {{-- Align with properties page: table + table-hover + align-middle --}}
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الوحدة</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th class="text-nowrap">المساحة</th>
                            <th>العقار</th>
                            <th>الموقع</th>
                            <th class="text-center" style="width: 220px;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $i => $unit)
                            <tr>
                                {{-- Row index (human-friendly, starts at 1) --}}
                                <td>{{ $i + 1 }}</td>

                                {{-- Unit name (fallback to em dash if null) --}}
                                <td>{{ $unit->name ?? '—' }}</td>

                                {{-- Unit type (capitalize for consistent look) --}}
                                <td class="text-capitalize">{{ $unit->type ?? '—' }}</td>

                                {{-- Status as Bootstrap badge, mapped to semantic colors --}}
                                <td>
                                    @php
                                        // Map unit statuses to Bootstrap contextual colors
                                        $status = $unit->status ?? 'unknown';
                                        $statusMap = [
                                            'available'         => 'success',
                                            'rented'            => 'warning',
                                            'sold'              => 'secondary',
                                            'under_maintenance' => 'danger',
                                        ];
                                        $badge = $statusMap[$status] ?? 'info';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ __($status) }}</span>
                                </td>

                                {{-- Area in square meters, formatted (fallback if null) --}}
                                <td>
                                    {{ is_numeric($unit->area ?? null) ? number_format($unit->area) . ' م²' : '—' }}
                                </td>

                                {{-- Related property summary (asset name + quick location) --}}
                                <td>
                                    {{-- Asset/Property name (polymorphic relation: unit -> property -> asset) --}}
                                    <div class="fw-semibold">{{ optional($unit->property?->asset)->name ?? '—' }}</div>
                                    <div class="text-muted small">
                                        {{ $unit->property->city ?? '—' }} - {{ $unit->property->neighborhood ?? '—' }}
                                    </div>
                                </td>

                                {{-- Location link to map (if available) --}}
                                <td>
                                    @if(!empty($unit->property?->url_location))
                                        <a href="{{ $unit->property->url_location }}"
                                           target="_blank" rel="noopener"
                                           class="text-decoration-none">
                                            <i class="fa-solid fa-location-dot"></i> خريطة
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Row actions (View / Edit / Delete) --}}
                                <td class="text-center">
                                    {{-- View --}}
                                    <a href="{{ route('units.show', $unit) }}"
                                       class="btn btn-sm btn-outline-info me-1" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('units.edit', $unit) }}"
                                       class="btn btn-sm btn-outline-primary me-1" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Delete (with CSRF + method spoofing) --}}
                                    <form action="{{ route('units.destroy', $unit) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="حذف">
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
