<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    // LISTA DE PRODUCTOS
    public function index(Request $request)
    {
        $query = Product::where('quantity', '>', 0)
            ->with(['colores', 'category']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->get();
        $categories = Category::orderBy('name')->get();

        return view('shop.index', compact('products', 'categories'));
    }

    // DETALLE DEL PRODUCTO
    public function show($id)
    {
        $product = Product::with(['colores', 'category', 'colorSizes'])->findOrFail($id);
        return view('shop.show', compact('product'));
    }

    // AGREGAR AL CARRITO / COMPRA
    public function submitProduct(Request $request, $id)
    {
        $user = auth()->user();
        $product = Product::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
            'size' => 'nullable|string|max:100',
            'color' => 'nullable|integer|exists:colores,id',
            'description' => 'nullable|string|max:500',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,jfif,webp|max:2048',
            'action' => 'required|in:buy_now,add_to_cart',
        ]);

        // GUARDAR IMÁGENES
        $imagenes = [];
        $path = public_path('images/shop_orders');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $nombre = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path, $nombre);
                $imagenes[] = $nombre;
            }
        }

        // EXTRA POR TALLA
        $extraSize = $this->getExtraBySize($request->size);

        $precioFinal = $product->price + $extraSize;
        $subtotal = $precioFinal * $request->quantity;

        $colorNombre = null;

        if ($request->color) {
            $color = \App\Models\Color::find($request->color);
            if ($color) {
                $colorNombre = $color->nombre;
            }
        }

        // GUARDAR EN CARRITO
        if (Auth::check() && Auth::user()->role === 'cliente') {

            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'customer_name' => $user->name,
                'customer_phone' => $user->phone,
                'quantity' => $request->quantity,
                'size' => $request->size,
                'color' => $colorNombre,
                'description' => $request->description,
                'reference_images' => $imagenes,
                'price' => $precioFinal,
                'subtotal' => $subtotal,
            ]);

        } else {
            $cart = session()->get('cart', []);

            $cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $precioFinal,
                'image' => $product->image,
                'quantity' => $request->quantity,
                'size' => $request->size,
                'color' => $colorNombre,
                'description' => $request->description,
                'reference_images' => $imagenes,
                'subtotal' => $subtotal,
            ];

            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart')
            ->with('success', 'Producto agregado correctamente.');

            if ($request->action === 'buy_now') {
                return redirect()->route('shop.cart')
                    ->with('success', 'Producto agregado. Continúa con tu compra.');
            }

            return redirect()->route('shop.cart')
                ->with('success', 'Producto agregado al carrito.');
    }

    // VER CARRITO
    public function cart()
    {
        $addresses = collect();

        if (Auth::check() && Auth::user()->role === 'cliente') {
            $cart = Cart::with(['items.product'])->firstOrCreate([
                'user_id' => Auth::id(),
            ]);

            $cartItems = $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name ?? 'Producto',
                    'price' => $item->price,
                    'image' => $item->product->image ?? null,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'color' => $item->color,
                    'description' => $item->description,
                    'reference_images' => $item->reference_images ?? [],
                    'subtotal' => $item->subtotal,
                ];
            })->toArray();

            $total = collect($cartItems)->sum('subtotal');
            $addresses = Auth::user()->addresses()->latest()->get();

            return view('shop.cart', compact('cartItems', 'total', 'addresses'));
        }

        $cart = session()->get('cart', []);
        $total = collect($cart)->sum('subtotal');

        return view('shop.cart', compact('cart', 'total', 'addresses'));
    }

    // PROCESAR PEDIDO
    public function processCheckout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $cart = Cart::with(['items.product'])->where('user_id', Auth::id())->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('shop.cart')->with('error', 'Carrito vacío.');
        }

        DB::beginTransaction();

        try {
            $total = $cart->items->sum('subtotal');

            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => Auth::user()->name,
                'total' => $total,
                'status' => 'pendiente',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'size' => $item->size,
                    'color' => $item->color,
                    'description' => $item->description,
                ]);
            }

            $cart->items()->delete();

            DB::commit();

            return redirect()->route('shop.cart')
                ->with('success', 'Pedido realizado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    private function getExtraBySize($size)
    {
        $size = strtoupper(trim($size));

        return match ($size) {
            'XL' => 15,
            'XXL' => 30,
            'XXXL' => 45,
            default => 0,
        };
    }

    public function removeFromCart($index)
{
    if (Auth::check() && Auth::user()->role === 'cliente') {

        $item = \App\Models\CartItem::findOrFail($index);
        $item->delete();

    } else {

        $cart = session()->get('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            session()->put('cart', $cart);
        }
    }

    return redirect()->route('shop.cart')
        ->with('success', 'Producto eliminado del carrito.');
}
}