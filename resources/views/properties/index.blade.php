@extends('layouts.app')
@section('title','العقارات')

@section('content')
    @include('components.global-errors')

    @if($properties->isEmpty())
        <div class="bg-white shadow-1 round-lg p-8 text-center">
            <div class="text-2xl font-extrabold mb-2">لا توجد عقارات</div>
            <div class="text-gray-500">استخدم شريط البحث العلوي أو أضف عقارًا جديدًا.</div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <div class="bg-white shadow-1 round-lg p-4 flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="text-sm text-[var(--text-lighter)]">الأصل</div>
                            <div class="font-semibold">{{ optional($property->asset)->name ?? '—' }}</div>
                        </div>
                        @php
                            $status = $property->status ?? null;
                            $statusColor = match($status){
                                'available' => 'bg-green-100 text-green-700',
                                'rented'    => 'bg-amber-100 text-amber-700',
                                'archived'  => 'bg-gray-100 text-gray-700',
                                default     => 'bg-blue-100 text-blue-700'
                            };
                        @endphp
                        @if($status)
                            <span class="text-xs px-2 py-1 rounded-md {{ $statusColor }}">{{ __($status) }}</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-md border border-[var(--border-color)] px-3 py-2">
                            <div class="text-[12px] text-[var(--text-lighter)] mb-1">الدولة</div>
                            <div class="font-semibold">{{ $property->country ?? '—' }}</div>
                        </div>
                        <div class="rounded-md border border-[var(--border-color)] px-3 py-2">
                            <div class="text-[12px] text-[var(--text-lighter)] mb-1">المدينة</div>
                            <div class="font-semibold">{{ $property->city ?? '—' }}</div>
                        </div>
                        <div class="rounded-md border border-[var(--border-color)] px-3 py-2">
                            <div class="text-[12px] text-[var(--text-lighter)] mb-1">الحي</div>
                            <div class="font-semibold">{{ $property->neighborhood ?? '—' }}</div>
                        </div>
                        <div class="rounded-md border border-[var(--border-color)] px-3 py-2">
                            <div class="text-[12px] text-[var(--text-lighter)] mb-1">المساحة</div>
                            <div class="font-semibold">{{ $property->area ?? '—' }}</div>
                        </div>
                    </div>

                    @if(!empty($property->url_location))
                        <a href="{{ $property->url_location }}" target="_blank" rel="noopener"
                           class="text-sm text-blue-600 hover:underline">موقع الخريطة</a>
                    @endif

                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('properties.show', $property->id) }}"
                           class="text-sm font-semibold text-blue-600 hover:underline">التفاصيل</a>
                        @can('update', $property)
                        <a href="{{ route('properties.edit', $property->id) }}"
                           class="text-sm font-semibold hover:underline">تعديل</a>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
@endsection


