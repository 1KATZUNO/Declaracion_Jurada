@props(['tipo' => 'info', 'mensaje'])
 @csrf
@php
$colores = [
  'success' => 'bg-green-100 text-green-800 border-green-300',
  'error'   => 'bg-red-100 text-red-800 border-red-300',
  'info'    => 'bg-blue-100 text-blue-800 border-blue-300',
  'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
];
@endphp

<div class="border-l-4 p-4 mb-5 rounded {{ $colores[$tipo] ?? $colores['info'] }}">
  {{ $mensaje }}
</div>
