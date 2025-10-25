@if (session('ok') || session('error') || $errors->any())
  <div role="status" aria-live="polite" class="mx-auto mb-4 max-w-7xl px-4">
    @if (session('ok'))
      <div class="flex items-start gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-800">
        <span class="mt-0.5 inline-block">✅</span>
        <p class="text-sm">{{ session('ok') }}</p>
      </div>
    @endif

    @if (session('error'))
      <div class="flex items-start gap-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <span class="mt-0.5 inline-block">⚠️</span>
        <p class="text-sm">{{ session('error') }}</p>
      </div>
    @endif

    @if ($errors->any())
      <div class="mt-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <p class="mb-1 text-sm font-medium">Por favor corrija los siguientes campos:</p>
        <ul class="list-inside list-disc text-sm">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>
@endif
