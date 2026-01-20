{{-- resources/views/admin/products/_table_rows.blade.php
     Devuelve solo las filas <tr> del listado de productos para respuestas AJAX --}}
<tbody class="divide-y divide-slate-200">
@foreach($products as $product)
    @php
        $isModel = is_object($product) && isset($product->id);
        $isArray = is_array($product) && isset($product['filename']);
        $rowId = null;
        $code = '—';
        $title = '—';
        $subtitle = null;
        $featured = false;
        $filenameForDelete = null;
        $imgUrlFinal = asset('images/placeholder-200x200.png');

        if ($isModel) {
            $rowId = $product->id;
            $code = $product->code ?? strtoupper(substr($product->title ?? $product->name ?? '',0,2));
            $title = $product->title ?? $product->name ?? '—';
            $subtitle = $product->subtitle ?? null;
            $featured = $product->featured ?? false;
            if (isset($product->image_url) && $product->image_url) {
                $imgUrlFinal = $product->image_url;
            } else {
                $img = $product->image ?? null;
                if ($img && \Illuminate\Support\Facades\Storage::disk('public')->exists('products/' . $img)) {
                    $imgUrlFinal = asset('storage/products/' . $img);
                } elseif ($img && file_exists(public_path('images/ateco/products/' . $img))) {
                    $imgUrlFinal = asset('images/ateco/products/' . $img);
                }
            }
        } elseif ($isArray) {
            $rowId = null;
            $code = strtoupper(substr($product['name'] ?? pathinfo($product['filename'], PATHINFO_FILENAME),0,2));
            $title = $product['name'] ?? pathinfo($product['filename'], PATHINFO_FILENAME);
            $filenameForDelete = $product['filename'];
            if (!empty($product['image'])) {
                $imgUrlFinal = $product['image'];
            } elseif (file_exists(public_path('images/ateco/products/' . $product['filename']))) {
                $imgUrlFinal = asset('images/ateco/products/' . $product['filename']);
            }
        } else {
            $rowId = $product->id ?? null;
            $code = strtoupper(substr($product->name ?? ($product['name'] ?? '—'), 0,2));
            $title = $product->title ?? $product->name ?? ($product['name'] ?? '—');
            if (!empty($product->image)) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists('products/' . $product->image)) {
                    $imgUrlFinal = asset('storage/products/' . $product->image);
                } elseif (file_exists(public_path('images/ateco/products/' . $product->image))) {
                    $imgUrlFinal = asset('images/ateco/products/' . $product->image);
                }
            }
        }
    @endphp

    <tr class="hover:bg-slate-50 transition">
        <td class="p-4 text-center font-mono uppercase text-slate-900">{{ $code }}</td>
        <td class="p-4 text-center">
            <img src="{{ $imgUrlFinal }}" class="h-20 w-20 object-cover rounded-md border mx-auto" alt="{{ $title }}">
        </td>
        <td class="p-4">
            <div class="text-slate-900 font-medium">{{ $title }}</div>
            @if($subtitle)
                <div class="text-sm text-slate-500">{{ $subtitle }}</div>
            @endif
        </td>
        <td class="p-4 text-center">
            @if($isModel)
                <button onclick="toggleDestacado(this)" data-id="{{ $rowId }}" id="destacado-{{ $rowId }}" class="text-xl transition transform hover:scale-110 cursor-pointer" title="Alternar destacado">
                    {{ $featured ? '⭐' : '—' }}
                </button>
            @else
                <span class="text-slate-400">—</span>
            @endif
        </td>
        <td class="p-4 text-center">
            <div class="flex items-center justify-center gap-4">
                @if($isModel)
                    <a href="{{ route('admin.products.edit', $rowId) }}" class="text-slate-500 hover:text-blue-600 transition cursor-pointer" title="Editar">
                        <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                @endif

                <button onclick="deleteProduct(this)" class="text-red-600 hover:text-red-700 transition cursor-pointer" title="Eliminar" aria-label="Eliminar producto {{ $title }}"
                    @if($isModel) data-id="{{ $rowId }}" @endif
                    @if($filenameForDelete) data-filename="{{ $filenameForDelete }}" @endif>
                    <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </td>
    </tr>
@endforeach
</tbody>
