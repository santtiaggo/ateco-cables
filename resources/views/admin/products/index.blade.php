@extends('layouts.admin')

@section('title','Productos | Admin')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="animate-[fadeIn_.3s_ease]">

<div class="space-y-10 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">

    <div class="flex justify-between items-center pb-2">
        <h2 class="text-2xl font-semibold text-slate-900">Productos Existentes (<span id="total-count">{{ $products->total() ?? (is_array($products) ? count($products) : 0) }}</span>)</h2>

        <a href="{{ url('/admin/products/create') }}" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Producto
        </a>
    </div>

    <p class="text-sm text-slate-500" id="stats-info">
        Página {{ $products->currentPage() ?? 1 }} de {{ $products->lastPage() ?? 1 }} —
        Mostrando {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} de {{ $products->total() ?? (is_array($products) ? count($products) : 0) }} productos
    </p>

    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
        <div class="relative w-full sm:w-1/3">
            <input type="text" id="search-input" value="{{ request('search','') }}" placeholder="Buscar producto..." class="w-full border border-slate-300 rounded-md pl-10 pr-3 py-2 text-sm bg-white
                          focus:ring-2 focus:ring-blue-600 focus:outline-none">

            <svg class="absolute top-2.5 left-3 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <div id="products-container">
        <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 text-xs uppercase">
                    <tr>
                        <th class="p-4 w-20 text-center font-medium">Orden</th>
                        <th class="p-4 w-32 text-center font-medium">Img</th>
                        <th class="p-4 font-medium text-start">Título</th>
                        <th class="p-4 w-20 text-center font-medium">Destacado</th>
                        <th class="p-4 w-40 text-center font-medium">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @foreach($products as $product)
                        @php
                            // determine shapes
                            $isModel = is_object($product) && isset($product->id);
                            $isArray = is_array($product) && isset($product['filename']);

                            // defaults
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
                                $filenameForDelete = null;

                                // Prefer accessor if present
                                if (isset($product->image_url) && $product->image_url) {
                                    $imgUrlFinal = $product->image_url;
                                } else {
                                    // check storage disk public
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
                                $subtitle = null;
                                $featured = false;
                                $filenameForDelete = $product['filename'];

                                // filesystem array provides an 'image' with absolute/asset already
                                if (!empty($product['image'])) {
                                    $imgUrlFinal = $product['image'];
                                } else {
                                    // fallback to public images path using filename
                                    if (file_exists(public_path('images/ateco/products/' . $product['filename']))) {
                                        $imgUrlFinal = asset('images/ateco/products/' . $product['filename']);
                                    }
                                }
                            } else {
                                // fallback generic
                                $rowId = $product->id ?? null;
                                $code = strtoupper(substr($product->name ?? ($product['name'] ?? '—'), 0,2));
                                $title = $product->title ?? $product->name ?? ($product['name'] ?? '—');
                                $featured = $product->featured ?? false;
                                // try common fields
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
                        {{-- Orden --}}
                        <td class="p-4 text-center font-mono uppercase text-slate-900">
                            {{ $code }}
                        </td>

                        {{-- Imagen --}}
                        <td class="p-4 text-center">
                            <img src="{{ $imgUrlFinal }}" class="h-20 w-20 object-cover rounded-md border mx-auto" alt="{{ $title }}">
                        </td>

                        {{-- Título --}}
                        <td class="p-4">
                            <div class="text-slate-900 font-medium">{{ $title }}</div>
                            @if($subtitle)
                                <div class="text-sm text-slate-500">{{ $subtitle }}</div>
                            @endif
                        </td>

                        {{-- Destacado (solo para DB/Eloquent) --}}
                        <td class="p-4 text-center">
                            @if($isModel)
                                <button
                                    onclick="toggleDestacado(this)"
                                    data-id="{{ $rowId }}"
                                    id="destacado-{{ $rowId }}"
                                    aria-pressed="{{ $featured ? 'true' : 'false' }}"
                                    class="text-xl transition transform hover:scale-110 cursor-pointer"
                                    title="Alternar destacado">
                                    <span class="icon">{{ $featured ? '⭐' : '—' }}</span>
                                </button>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-4">
                                @if($isModel)
                                    <a href="{{ route('admin.products.edit', $rowId) }}" class="text-slate-500 hover:text-blue-600 transition cursor-pointer" title="Editar">
                                        <!-- icono editar -->
                                        <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                @endif

                                <!-- Delete button: si es model usa id, si es archivo usa filename -->
                                <button onclick="deleteProduct(this)" class="text-red-600 hover:text-red-700 transition cursor-pointer" title="Eliminar" aria-label="Eliminar producto {{ $title }}"
                                    @if($isModel) data-id="{{ $rowId }}" @endif
                                    @if($filenameForDelete) data-filename="{{ $filenameForDelete }}" @endif>
                                    <!-- icono eliminar -->
                                    <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

    <div id="pagination-container" class="flex justify-center pt-4">
        {{-- Render the initial pagination HTML if paginator, otherwise blank --}}
        @if(method_exists($products, 'links'))
            {{ $products->withQueryString()->links() }}
        @endif
    </div>

</div>

<style>
@keyframes fadeIn { from {opacity:0; transform:translateY(6px)} to {opacity:1; transform:translateY(0)} }
.animate-fadeIn { animation: fadeIn .28s ease; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const productsContainer = document.getElementById('products-container');
    const paginationContainer = document.getElementById('pagination-container');
    const totalCount = document.getElementById('total-count');
    const statsInfo = document.getElementById('stats-info');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadProducts(1);
        }, 300);
    });

    function loadProducts(page = 1) {
        const search = searchInput.value;
        const url = `/admin/products?search=${encodeURIComponent(search)}&page=${page}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) productsContainer.innerHTML = data.html;
            if (data.pagination) paginationContainer.innerHTML = data.pagination;
            if (data.stats) {
                totalCount.textContent = data.stats.total;
                statsInfo.textContent = `Página ${data.stats.current_page} de ${data.stats.last_page} — Mostrando ${data.stats.first_item}–${data.stats.last_item} de ${data.stats.total} productos`;
            }

            const newUrl = new URL(window.location);
            newUrl.searchParams.set('search', search);
            newUrl.searchParams.set('page', page);
            window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al cargar productos', 'error');
        });
    }

    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination-link');
        if (paginationLink) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            const page = url.searchParams.get('page') || 1;
            loadProducts(page);
        }
    });

    // toggleDestacado acepta tanto el elemento (onclick="toggleDestacado(this)")
    // como un id (toggleDestacado(123))
    window.toggleDestacado = function(elOrId) {
        // resolver elemento y id
        let el;
        let id;
        if (typeof elOrId === 'object' && elOrId !== null) {
            el = elOrId;
            id = el.dataset.id;
        } else {
            id = elOrId;
            el = document.querySelector(`#destacado-${id}`);
        }
        if (!id) return;

        fetch(`/admin/products/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // actualizar UI: texto y aria-pressed
                if (el) {
                    const iconSpan = el.querySelector('.icon');
                    if (iconSpan) iconSpan.textContent = data.destacado ? '⭐' : '—';
                    el.setAttribute('aria-pressed', data.destacado ? 'true' : 'false');
                }
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'No se pudo actualizar', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al actualizar', 'error');
        });
    };

    window.deleteProduct = function(buttonEl) {
        let id = buttonEl.dataset.id;
        let filename = buttonEl.dataset.filename;

        if (!confirm('¿Estás seguro de eliminar este producto?')) return;

        let url, method = 'DELETE';
        if (id) {
            url = `/admin/products/${id}`;
        } else if (filename) {
            // endpoint para eliminar archivo (filesystem mode)
            url = `/admin/products/file/${encodeURIComponent(filename)}`;
        } else {
            console.error('No id or filename found for delete');
            return;
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                loadProducts(1);
            } else {
                showToast(data.message || 'Error al eliminar', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al eliminar', 'error');
        });
    };

    function showToast(message, type = 'success') {
        const event = new CustomEvent('toast', {
            detail: { message, type }
        });
        window.dispatchEvent(event);
    }
});
</script>
@endpush

</div>
@endsection
