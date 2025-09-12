@php
  // الحقول التي لا نريد عرض أخطائها هنا لأننا نظهرها تحت الحقل
  $skip = $skip ?? [];
  $otherErrors = collect($errors->getMessages())->except($skip)->flatten();
@endphp

@if (session('error'))
  <div class="alert alert-danger small">{{ session('error') }}</div>
@endif

@if ($otherErrors->isNotEmpty())
  <div class="alert alert-danger small">
    @foreach ($otherErrors as $msg)
      <div>{{ $msg }}</div>
    @endforeach
  </div>
@endif
