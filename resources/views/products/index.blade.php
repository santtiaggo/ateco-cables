@extends('layouts.app')
@section('title', 'Productos')
@section('content')
<div class="container mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold mb-6">Productos</h1>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($products as $product)
      <x-product-card :product="$product" />
    @endforeach
  </div>
  <div class="mt-6">{{ $products->links() }}</div>
</div>
@endsection
