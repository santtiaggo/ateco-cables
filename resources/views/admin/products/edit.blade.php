@extends('layouts.admin')

@section('title','Editar Producto | Admin')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="animate-[fadeIn_.3s_ease]">
    <div class="p-6 mx-auto space-y-10 animate-fadeIn">

        <div class="flex justify-between items-center pb-4">
            <h2 class="text-2xl font-bold text-slate-900">Editar Producto</h2>

            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition cursor-pointer">
                ← Volver
            </a>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-6 space-y-8">
            @csrf
            @method('PUT')

            {{-- TITLE / NAME --}}
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-800">Título</label>
                <input type="text" name="title" value="{{ old('title', $product->title ?? $product->name ?? '') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white
                              focus:ring-2 focus:ring-blue-600 focus:outline-none" required>
                @error('title') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- DESCRIPTION --}}
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-800">Descripción</label>

                <textarea name="description" id="description" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none"> {!! old('description', $product->description ?? '') !!}</textarea>

                @error('description')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Orden / Código --}} 
            @php use Illuminate\Support\Facades\Schema; @endphp
            @if(Schema::hasTable('products') && Schema::hasColumn('products','code'))
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-800">Orden / Código</label>
                <input type="text" name="code" value="{{ old('code', $product->code ?? '') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white uppercase font-mono
                              focus:ring-2 focus:ring-blue-600 focus:outline-none">
                @error('code') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
            @endif

            {{-- Featured --}}
            @if(Schema::hasTable('products') && Schema::hasColumn('products','featured'))
            <div class="flex items-center gap-3">
                <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured ?? false) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-600 cursor-pointer">
                <label class="text-sm font-medium text-slate-800">Destacado</label>
            </div>
            @endif

            {{-- CURRENT IMAGE / UPLOAD --}}
            <div class="space-y-3">
                <label class="text-sm font-medium text-slate-800">Imagen Actual</label>

                <div>
                    <img id="current-image" src="{{ $product->image_url ?? asset('images/placeholder-200x200.png') }}" class="w-40 h-40 object-cover rounded-md border border-slate-200" alt="Imagen actual">
                </div>

                <label class="block text-sm font-medium text-slate-800 mt-4">Nueva Imagen</label>

                <img id="preview-image" class="hidden w-40 h-40 object-cover rounded-md border border-slate-200 mb-2" alt="Preview">

                <input type="file" name="image" id="imageInput" class="hidden" accept="image/*">

                <p class="text-xs text-slate-500 leading-relaxed">
                    Resolución recomendada: 800×600px<br>
                    Peso máximo: 2MB<br>
                    Formatos permitidos: WEBP, PNG, JPG
                </p>

                <button type="button" onclick="document.getElementById('imageInput').click()" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                    Subir Nueva Imagen
                </button>

                @error('image') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition cursor-pointer">
                    Cancelar
                </a>

                <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition
                               cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Actualizar Producto
                </button>
            </div>
        </form>

    </div>

    <style>
    @keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
    .animate-fadeIn { animation: fadeIn .35s ease; }
    </style>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1) Insertamos el contenido del servidor en el textarea de forma segura ---
        // Usa old() si viene de un post fallido, si no toma la descripción del producto.
        const initialDescription = @json(old('description', $product->description ?? ''));

        // Si hay contenido real, volcamos en el textarea (trim para evitar solo espacios)
        const textarea = document.getElementById('description');
        if (textarea && (textarea.value === '' || textarea.value.trim() === '')) {
            // Asignamos el HTML crudo (no escapado)
            textarea.value = initialDescription ?? '';
        }

        // --- 2) Inicializamos CKEditor SOLO DESPUÉS de haber volcado el contenido ---
        try {
            if (window.ClassicEditor) {
                ClassicEditor
                    .create(document.querySelector('#description'))
                    .catch(error => console.error('CKEditor error:', error));
            }
        } catch (e) {
            console.warn('CKEditor no está disponible', e);
        }

        // --- 3) Preview de imagen (mantengo tu lógica) ---
        const imageInput = document.getElementById('imageInput');
        const previewImage = document.getElementById('preview-image');
        const currentImage = document.getElementById('current-image');
        const submitBtn = document.getElementById('submitBtn');

        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert('Por favor selecciona una imagen válida');
                    this.value = '';
                    return;
                }

                if (file.size > 2048 * 1024) {
                    alert('La imagen no debe superar 2MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                    if (currentImage) currentImage.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        }

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Actualizando...';
                }
            });
        }
    });
    </script>
    @endpush

</div>
@endsection
