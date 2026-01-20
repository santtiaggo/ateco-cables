@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('assets/img/placeholder.png') }}" alt="{{ $product->name }}" class="w-full h-96 object-cover mb-6">
      <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
      <p class="mt-4 text-gray-700">{{ $product->description }}</p>
    </div>
    <aside class="p-4 border rounded">
      <p class="text-xl font-bold mb-4">${{ number_format($product->price, 2) }}</p>
      <a href="#contact" class="block bg-blue-600 text-white text-center py-2 rounded">Contactar</a>
    </aside>
  </div>
</div>
@endsection
