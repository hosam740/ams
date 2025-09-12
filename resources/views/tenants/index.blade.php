@extends('layouts.app')

@section('title', 'قائمة المستأجرين')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">المستأجرون</h4>
    <div>
        <a href="{{ route('tenants.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> إضافة مستأجر
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($tenants->isEmpty())
    <div class="alert alert-info">لا توجد سجلات مستأجرين حالياً.</div>
@else
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
                    <td>{{ $tenant->first_name }} {{ $tenant->last_name }}</td>
                    <td>{{ $tenant->national_id }}</td>
                    <td>{{ $tenant->phone_number }}</td>
                    <td>{{ $tenant->nationality }}</td>
                    <td class="text-center">
                        {{-- عرض --}}
                        <a href="{{ route('tenants.show', $tenant) }}"
                           class="btn btn-outline-secondary btn-sm me-1" title="عرض">
                            <i class="bi bi-eye"></i>
                        </a>

                        {{-- تعديل --}}
                        <a href="{{ route('tenants.edit', $tenant) }}"
                           class="btn btn-outline-primary btn-sm me-1" title="تعديل">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        {{-- حذف --}}
                        <form action="{{ route('tenants.destroy', $tenant) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المستأجر؟');">
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
