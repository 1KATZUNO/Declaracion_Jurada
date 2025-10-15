@props(['href' => '#', 'color' => 'indigo'])

@php
$colores = [
  'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
  'indigo' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
  'green' => 'bg-green-600 hover:bg-green-700 text-white',
  'yellow' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
  'red' => 'bg-red-600 hover:bg-red-700 text-white',
];
@endphp

<a href="{{ $href }}" class="inline-block px-4 py-2 rounded-lg font-semibold shadow {{ $colores[$color] ?? $colores['indigo'] }}">
  {{ $slot }}
</a>
