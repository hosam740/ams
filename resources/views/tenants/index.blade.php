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
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">الاسم</th>
                            <th style="width: 15%;">رقم الهوية/الإقامة</th>
                            <th style="width: 15%;">رقم الجوال</th>
                            <th style="width: 30%;">البريد الإلكتروني</th>
                            <th style="width: 15%;">الجنسية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $i => $tenant)
                            <tr>
                                <td colspan="6" class="p-0">
                                    <a href="{{ route('tenants.show', $tenant) }}" class="text-decoration-none text-dark d-flex align-items-center" style="padding: 0.5rem;">
                                        <div style="width: 5%;">{{ $i + 1 }}</div>
                                        <div style="width: 20%;" class="fw-semibold">
                                            {{ trim(($tenant->first_name ?? '').' '.($tenant->last_name ?? '')) ?: '—' }}
                                        </div>
                                        <div style="width: 15%;">{{ $tenant->national_id ?? '—' }}</div>
                                        <div style="width: 15%;">{{ $tenant->phone_number ?? '—' }}</div>
                                        <div style="width: 30%;" class="text-muted small">{{ $tenant->email ?? '—' }}</div>
                                        <div style="width: 15%;" class="text-capitalize">{{ $tenant->nationality ?? '—' }}</div>
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
