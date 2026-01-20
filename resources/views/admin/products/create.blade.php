{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.admin')

@section('title','Crear Producto | Admin')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="animate-[fadeIn_.3s_ease]">
    <div class="p-6 mx-auto space-y-10 animate-fadeIn">

        <div class="flex justify-between items-center pb-4">
            <h2 class="text-2xl font-bold text-slate-900">Crear Producto</h2>

            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition cursor-pointer">
                ← Volver
            </a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-6 space-y-8">
            @csrf

            {{-- Título --}}
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-800">Título</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                @error('title') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Descripción (rich) --}}
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-800">Descripción</label>

                <textarea name="description" id="description" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('description') }}</textarea>
                @error('description') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Orden / Código --}}
            <div class="space-y-1">
                <label class="text-sm font-medium text-slate-800">Orden (código)</label>
                <input type="text" name="code" value="{{ old('code') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white uppercase font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">
                @error('code') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Destacado --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" name="featured" id="featured" value="1" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-600 focus:outline-none cursor-pointer" {{ old('featured') ? 'checked' : '' }}>
                <label for="featured" class="text-sm font-medium text-slate-800">Destacado</label>
            </div>

            {{-- Imagen principal --}}
            <div class="space-y-3">
                <label class="text-sm font-medium text-slate-800">Imagen Principal</label>

                <div id="image-preview-container" class="w-[200px] h-[200px] bg-slate-50 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-400 text-sm">
                    <span id="image-preview-text">Subir imagen principal</span>
                    <img id="preview-image" class="hidden w-full h-full object-contain rounded-md" alt="Preview">
                </div>

                <input type="file" name="image" id="imageInput" class="hidden" accept="image/*">

                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.getElementById('imageInput').click()" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                        Seleccionar Imagen
                    </button>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Recomendado: 800×600px • JPG/WEBP • máx 5MB
                    </p>
                </div>

                @error('image') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Acciones --}}
            <div class="pt-4 border-t border-slate-200 flex justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition cursor-pointer">
                    Cancelar
                </a>

                <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Crear Producto
                </button>
            </div>

        </form>
    </div>

    <style>
    @keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
    .animate-fadeIn { animation: fadeIn .35s ease; }
    </style>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        if (window.ClassicEditor) {
            ClassicEditor
                .create(document.querySelector('#description'))
                .catch(error => { console.error(error); });
        }
    } catch (e) {
    }

    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('preview-image');
    const previewText = document.getElementById('image-preview-text');
    const previewContainer = document.getElementById('image-preview-container');
    const submitBtn = document.getElementById('submitBtn');

    const MAX_BYTES = 5 * 1024 * 1024; 

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Por favor selecciona una imagen válida');
            this.value = '';
            previewImage.classList.add('hidden');
            previewText.classList.remove('hidden');
            previewContainer.classList.add('border-dashed');
            return;
        }

        if (file.size > MAX_BYTES) {
            alert('La imagen no debe superar 5MB');
            this.value = '';
            previewImage.classList.add('hidden');
            previewText.classList.remove('hidden');
            previewContainer.classList.add('border-dashed');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.classList.remove('hidden');
            previewText.classList.add('hidden');
            previewContainer.classList.remove('border-dashed');
        };
        reader.readAsDataURL(file);
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creando...';
    });
});
</script>
@endpush
