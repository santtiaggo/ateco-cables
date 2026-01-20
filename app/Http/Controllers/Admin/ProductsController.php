<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    protected $publicDir = 'images/ateco/products'; 

    public function home()
    {
        if (Schema::hasTable('products')) {
            $featured = Product::where('featured', true)->orderBy('updated_at','desc')->take(6)->get();
            if ($featured->isEmpty()) {
                $featured = Product::orderBy('id','desc')->take(10)->get();
            }
            return view('home', compact('featured'));
        }

        $dir = public_path('images/ateco/products');
        $files = [];
        if (File::exists($dir)) {
            $files = collect(File::files($dir))
                ->sortByDesc(fn($f) => $f->getMTime())
                ->take(10)
                ->map(fn($f) => [
                    'name' => ucwords(str_replace(['-','_'], ' ', pathinfo($f->getFilename(), PATHINFO_FILENAME))),
                    'image' => asset('images/ateco/products/' . $f->getFilename()),
                    'filename' => $f->getFilename(),
                ])->values();
        }
        $featured = $files;
        return view('home', compact('featured'));
    }

    public function catalog(Request $request)
    {
        if (Schema::hasTable('products')) {
            $products = Product::orderBy('id','desc')->paginate(12);
            return view('products.catalog', compact('products'));
        }

        $dir = public_path('images/ateco/products');
        $files = [];
        if (File::exists($dir)) {
            $all = collect(File::files($dir))
                ->sortByDesc(fn($f) => $f->getMTime())
                ->map(fn($f) => [
                    'name' => ucwords(str_replace(['-','_'], ' ', pathinfo($f->getFilename(), PATHINFO_FILENAME))),
                    'image' => asset('images/ateco/products/' . $f->getFilename()),
                    'filename' => $f->getFilename(),
                ])->values();
        } else {
            $all = collect();
        }

        $page = $request->get('page', 1);
        $perPage = 12;
        $slice = $all->slice(($page - 1) * $perPage, $perPage)->values();
        $products = new LengthAwarePaginator($slice, $all->count(), $perPage, $page, [
            'path' => url()->current(),
            'query' => $request->query(),
        ]);

        return view('products.catalog', compact('products'));
    }

    public function index(Request $request)
    {
        $search = $request->get('search','');

        if (Schema::hasTable('products') && Product::count() > 0) {
            $query = Product::query();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('subtitle', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $products = $query->orderBy('id', 'desc')->paginate(12);

            if ($request->ajax()) {
                $html = view('admin.products._table_rows', compact('products'))->render();
                $pagination = view('admin.products._pagination', compact('products'))->render();

                $stats = [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'first_item' => $products->firstItem(),
                    'last_item' => $products->lastItem(),
                ];

                return response()->json([
                    'html' => $html,
                    'pagination' => $pagination,
                    'stats' => $stats,
                ]);
            }

            return view('admin.products.index', compact('products'));
        }

        $path = public_path($this->publicDir);
        $items = [];

        if (File::exists($path)) {
            $files = collect(File::files($path))
                ->map(function($f){
                    $filename = $f->getFilename();
                    return [
                        'filename' => $filename,
                        'name' => ucwords(str_replace(['-','_'], ' ', pathinfo($filename, PATHINFO_FILENAME))),
                        'image' => asset($this->publicDir . '/' . $filename),
                    ];
                });

            if ($search) {
                $files = $files->filter(function($item) use ($search){
                    return stripos($item['name'], $search) !== false;
                })->values();
            }

            $files = $files->values()->all();
            $items = $files;
        }

        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($page - 1) * $perPage;
        $currentItems = array_slice($items, $offset, $perPage);
        $paginator = new LengthAwarePaginator($currentItems, count($items), $perPage, $page, [
            'path' => url()->current(),
            'query' => $request->query(),
        ]);

        if ($request->ajax()) {
            $html = view('admin.products._table_rows', ['products' => $paginator])->render();
            $pagination = view('admin.products._pagination', ['products' => $paginator])->render();

            $stats = [
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'first_item' => $paginator->firstItem(),
                'last_item' => $paginator->lastItem(),
            ];

            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'stats' => $stats,
            ]);
        }

        return view('admin.products.index', ['products' => $paginator]);
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required_without:image|string|max:255',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:500',
            'code' => 'nullable|string|max:50',
            'featured' => 'nullable|boolean',
            'slug' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        // imagen
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = $filename;
        }

        // mapear title -> name si aplica
        $columns = Schema::hasTable('products') ? Schema::getColumnListing('products') : [];
        if (in_array('name', $columns) && isset($validated['title'])) {
            $validated['name'] = $validated['title'];
            // no unsetear title aún, lo usamos para slug
        }

        // Generar slug si la tabla tiene columna slug
        if (in_array('slug', $columns)) {
            $base = $validated['slug'] ?? ($validated['title'] ?? $validated['name'] ?? 'product');
            $slug = Str::slug($base);
            // asegurar unicidad
            $original = $slug;
            $i = 1;
            while (Schema::hasTable('products') && Product::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $i;
                $i++;
            }
            $validated['slug'] = $slug;
        }

        // Filtrar sólo columnas existentes
        if (!empty($columns)) {
            $validated = array_intersect_key($validated, array_flip($columns));
        }

        // Asegurar featured si existe
        if (in_array('featured', $columns)) {
            $validated['featured'] = isset($validated['featured']) ? (bool)$validated['featured'] : false;
        } else {
            unset($validated['featured']);
        }

        // Crear registro si la tabla existe
        if (!empty($columns)) {
            Product::create($validated);
            return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente.');
        }

        // fallback filesystem (sin DB)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $dest = public_path('images/ateco/products');
            if (!File::exists($dest)) File::makeDirectory($dest, 0755, true);
            $file->move($dest, $filename);
            return redirect()->route('admin.products.index')->with('success', 'Imagen subida correctamente.');
        }

        return redirect()->route('admin.products.index')->with('error', 'No se pudo crear el producto.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:500',
            'code' => 'nullable|string|max:50',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        // manejar nueva imagen
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete('products/' . $product->image);
            }
            $file = $request->file('image');
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = $filename;
        }

        // Lista de columnas real
        $columns = Schema::hasTable('products') ? Schema::getColumnListing('products') : [];

        // Mapear title -> name según columnas
        if (in_array('name', $columns) && isset($validated['title'])) {
            $validated['name'] = $validated['title'];
            unset($validated['title']);
        }

        // Filtrar sólo columnas existentes
        if (!empty($columns)) {
            $validated = array_intersect_key($validated, array_flip($columns));
        }

        // Asegurar featured solo si existe
        if (in_array('featured', $columns)) {
            $validated['featured'] = isset($validated['featured']) ? (bool)$validated['featured'] : false;
        } else {
            // si no existe, no incluirlo (evita el error)
            unset($validated['featured']);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function toggle(Product $product): JsonResponse
    {
        // Seguridad: si el campo no existe, evitamos el crash
        if (!array_key_exists('featured', $product->getAttributes())) {
            return response()->json([
                'success' => false,
                'message' => 'El campo featured no existe en el producto'
            ], 500);
        }

        // toggle
        $product->featured = ! (bool) $product->featured;
        $product->save();

        return response()->json([
            'success'   => true,
            'destacado'=> (bool) $product->featured,
            'message'  => $product->featured
                ? 'Producto destacado'
                : 'Producto quitado de destacados'
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    }

    public function destroyFile($filename)
    {
        $safe = urldecode($filename);
        $file = public_path($this->publicDir . '/' . $safe);

        if (File::exists($file)) {
            File::delete($file);
            return response()->json(['success' => true, 'message' => 'Imagen eliminada.']);
        }

        return response()->json(['success' => false, 'message' => 'Archivo no encontrado.'], 404);
    }
}
