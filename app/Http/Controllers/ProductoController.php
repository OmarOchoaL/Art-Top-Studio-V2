<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Color;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\ProductColorSize;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Product::with(['category', 'colores'])->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $colores = Color::all();
        $categorias = Category::all();

        return view('productos.create', compact('colores', 'categorias'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'quantity' => 'required|integer|min:0',
        'category_id' => 'required',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'nuevo_color' => 'nullable|string|max:255',
        'nuevo_color_hex' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);

    DB::beginTransaction();

    try {
        $imageName = null;
        $path = public_path('images/products');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $imageName);
        }

        $producto = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'quantity'    => $request->quantity,
            'size'        => $request->size,
            'category_id' => $request->category_id,
            'image'       => $imageName
        ]);

        $coloresFinales = $request->colores ?? [];

        if ($request->filled('nuevo_color')) {
            $nombreColor = trim($request->nuevo_color);
            $hex = $request->filled('nuevo_color_hex') ? $request->nuevo_color_hex : '#000000';

            $nuevoColor = Color::where('nombre', $nombreColor)->first();

            if (!$nuevoColor) {
                $nuevoColor = Color::create([
                    'nombre' => $nombreColor,
                    'codigo_hex' => $hex,
                ]);
            } else {
                if (empty($nuevoColor->codigo_hex)) {
                    $nuevoColor->codigo_hex = $hex;
                    $nuevoColor->save();
                }
            }

            $coloresFinales[] = $nuevoColor->id;
        }
        $coloresData = [];
        $totalStock = 0;

        foreach ($coloresFinales as $colorId) {
            $cantidadColor = 0;

            if ($request->filled('size')) {
                $sizes = explode(',', $request->size);

                foreach ($sizes as $size) {
                    $size = trim($size);

                    if ($size === '') {
                        continue;
                    }

                    $cantidadSize = (int) $request->input('cantidad_color_size.' . $colorId . '.' . $size, 0);

                    if ($cantidadSize > 0) {
                        ProductColorSize::create([
                            'product_id' => $producto->id,
                            'color_id' => $colorId,
                            'size' => $size,
                            'quantity' => $cantidadSize,
                        ]);
                    }

                    $cantidadColor += $cantidadSize;
                }
            } else {
                $cantidadColor = (int) $request->input('cantidad_color.' . $colorId, 0);
            }

            $coloresData[$colorId] = [
                'cantidad' => $cantidadColor
            ];

            $totalStock += $cantidadColor;
        }

        $producto->colores()->sync($coloresData);

        $producto->update([
            'quantity' => $totalStock,
        ]);

        DB::commit();
        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente');

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors(['error' => 'No se pudo guardar el producto: ' . $e->getMessage()]);
    }

    
}

    public function edit(Product $producto)
    {
        $producto->load('colorSizes');

        $categorias = Category::all();
        $colores = Color::all();

        return view('productos.edit', compact(
            'producto',
            'categorias',
            'colores'
        ));
    }

    public function update(Request $request, Product $producto)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'quantity' => 'required|integer|min:0',
        'category_id' => 'required',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'nuevo_color' => 'nullable|string|max:255',
        'nuevo_color_hex' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);

    DB::beginTransaction();

    try {
        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'quantity'    => $request->quantity,
            'size'        => $request->size,
            'category_id' => $request->category_id
        ];

        $path = public_path('images/products');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($request->hasFile('image')) {
            if ($producto->image && file_exists($path . '/' . $producto->image)) {
                unlink($path . '/' . $producto->image);
            }

            $file = $request->file('image');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $imageName);

            $data['image'] = $imageName;
        }

        $producto->update($data);

        $coloresFinales = $request->colores ?? [];

        if ($request->filled('nuevo_color')) {
            $nombreColor = trim($request->nuevo_color);
            $hex = $request->filled('nuevo_color_hex') ? $request->nuevo_color_hex : '#000000';

            $nuevoColor = Color::where('nombre', $nombreColor)->first();

            if (!$nuevoColor) {
                $nuevoColor = Color::create([
                    'nombre' => $nombreColor,
                    'codigo_hex' => $hex,
                ]);
            } else {
                if (empty($nuevoColor->codigo_hex)) {
                    $nuevoColor->codigo_hex = $hex;
                    $nuevoColor->save();
                }
            }

            $coloresFinales[] = $nuevoColor->id;
        }

        $producto->colorSizes()->delete();

        $coloresData = [];
        $totalStock = 0;

        foreach ($coloresFinales as $colorId) {
            $cantidadColor = 0;

            if ($request->filled('size')) {
                $sizes = explode(',', $request->size);

                foreach ($sizes as $size) {
                    $size = trim($size);

                    if ($size === '') {
                        continue;
                    }

                    $cantidadSize = (int) $request->input('cantidad_color_size.' . $colorId . '.' . $size, 0);

                    if ($cantidadSize > 0) {
                        ProductColorSize::create([
                            'product_id' => $producto->id,
                            'color_id' => $colorId,
                            'size' => $size,
                            'quantity' => $cantidadSize,
                        ]);
                    }

                    $cantidadColor += $cantidadSize;
                }
            } else {
                $cantidadColor = (int) $request->input('cantidad_color.' . $colorId, 0);
            }

            $coloresData[$colorId] = [
                'cantidad' => $cantidadColor
            ];

            $totalStock += $cantidadColor;
        }

        $producto->colores()->sync($coloresData);

        $producto->update([
            'quantity' => $totalStock,
        ]);

                DB::commit();

                return redirect()->route('productos.index')
                    ->with('success', 'Producto actualizado correctamente');

            } catch (\Throwable $e) {
                DB::rollBack();

                return back()
                    ->withInput()
                    ->withErrors(['error' => 'No se pudo actualizar el producto: ' . $e->getMessage()]);
            }

            
}

    public function destroy(Product $producto)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'No tienes permiso para eliminar productos.');
        }
        
        $producto->colores()->detach();

        $path = public_path('images/products');

        if ($producto->image && file_exists($path . '/' . $producto->image)) {
            unlink($path . '/' . $producto->image);
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }

    public function options($id)
    {
        $product = Product::with('options')->findOrFail($id);
        return response()->json($product->options);
    }

    public function show($id)
    {
        $product = Product::with('colores')->findOrFail($id);
        return response()->json($product);
    }
}