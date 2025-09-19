@extends('layouts.app')
@section('title','قائمة المستأجرين')

@section('content')
    {{-- Global errors --}}
    @include('components.global-errors')

    {{-- Page container --}}
    <div class="bg-white shadow-1 round-lg p-4">

        {{-- Header + primary action --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-users me-2 text-primary"></i> قائمة المستأجرين
            </h3>
            <a href="{{ route('tenants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة مستأجر
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
        @if($tenants->isEmpty())
            <div class="alert alert-info mb-0">لا توجد سجلات مستأجرين حالياً.</div>
        @else
            {{-- Data table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th class="text-nowrap">رقم الهوية/الإقامة</th>
                            <th class="text-nowrap">رقم الجوال</th>
                            <th>الجنسية</th>
                            <th class="text-center" style="width: 220px;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $i => $tenant)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ trim(($tenant->first_name ?? '').' '.($tenant->last_name ?? '')) ?: '—' }}</td>
                                <td>{{ $tenant->national_id ?? '—' }}</td>
                                <td>{{ $tenant->phone_number ?? '—' }}</td>
                                <td class="text-capitalize">{{ $tenant->nationality ?? '—' }}</td>
                                <td class="text-center">
                                    {{-- View --}}
                                    <a href="{{ route('tenants.show', $tenant) }}"
                                       class="btn btn-sm btn-outline-info me-1" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('tenants.edit', $tenant) }}"
                                       class="btn btn-sm btn-outline-primary me-1" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('tenants.destroy', $tenant) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المستأجر؟');">
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
