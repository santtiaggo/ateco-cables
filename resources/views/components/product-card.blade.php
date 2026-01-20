<div class="border rounded p-4 hover:shadow-lg">
  <a href="{{ route('products.show', $product) }}">
    <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('assets/img/placeholder.png') }}" alt="{{ $product->name }}" class="w-full h-48 object-cover mb-3">
    <h3 class="font-semibold">{{ $product->name }}</h3>
    <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
    <p class="mt-2 font-bold">${{ number_format($product->price, 2) }}</p>
  </a>
</div>
