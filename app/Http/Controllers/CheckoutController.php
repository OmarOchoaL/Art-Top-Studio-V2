<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Cart;

class CheckoutController extends Controller
{
    private function addressPayload(Request $request): array
    {
        return [
            'user_id' => Auth::id(),

            // compatibilidad con estructura vieja
            'name' => $request->alias,
            'postal_code' => $request->zip_code,

            // estructura nueva
            'alias' => $request->alias,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'street' => $request->street,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'references' => $request->references,
        ];
    }

    private function getCartData()
    {
        if (Auth::check() && Auth::user()->role === 'cliente') {
            $cart = Cart::with(['items.product'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$cart || $cart->items->count() === 0) {
                return [
                    'items' => [],
                    'total' => 0,
                    'db_cart' => true,
                ];
            }

            $items = $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name ?? 'Producto',
                    'price' => $item->price,
                    'image' => $item->product->image ?? null,
                    'customer_name' => $item->customer_name,
                    'customer_phone' => $item->customer_phone,
                    'quantity' => $item->quantity,
                    'size' => $item->size,
                    'color' => $item->color,
                    'description' => $item->description,
                    'reference_images' => $item->reference_images ?? [],
                    'subtotal' => $item->subtotal,
                ];
            })->toArray();

            return [
                'items' => $items,
                'total' => collect($items)->sum('subtotal'),
                'db_cart' => true,
            ];
        }

        $cart = session()->get('cart', []);

        return [
            'items' => $cart,
            'total' => collect($cart)->sum('subtotal'),
            'db_cart' => false,
        ];
    }

    public function address()
    {
        if (!Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login');
        }

        $cartData = $this->getCartData();
        $cart = $cartData['items'];

        if (count($cart) === 0) {
            return redirect()->route('shop.cart')->with('error', 'Tu carrito está vacío.');
        }

        $addresses = Auth::user()->addresses()->latest()->get();

        return view('shop.checkout_address', compact('cart', 'addresses'));
    }

    public function saveAddress(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login');
        }

        $request->validate([
            'alias' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'street' => 'required|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'references' => 'nullable|string',
        ]);

        $address = Address::create($this->addressPayload($request));

        session(['selected_address_id' => $address->id]);

        return redirect()->route('shop.checkout.summary')
            ->with('success', 'Dirección guardada correctamente.');
    }

    public function selectAddress(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login');
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $address = Address::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        session(['selected_address_id' => $address->id]);

        return redirect()->route('shop.checkout.summary');
    }

    public function summary()
    {
        if (!Auth::check() || Auth::user()->role !== 'cliente') {
            return redirect()->route('login');
        }

        $cartData = $this->getCartData();
        $cart = $cartData['items'];
        $total = $cartData['total'];
        $selectedAddressId = session('selected_address_id');

        if (count($cart) === 0) {
            return redirect()->route('shop.cart')->with('error', 'Tu carrito está vacío.');
        }

        if (!$selectedAddressId) {
            return redirect()->route('shop.checkout')->with('error', 'Selecciona una dirección.');
        }

        $selectedAddress = Address::where('id', $selectedAddressId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('shop.checkout_summary', compact('cart', 'selectedAddress', 'total'));
    }
}