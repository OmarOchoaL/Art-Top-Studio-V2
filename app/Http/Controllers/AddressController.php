<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    private function addressPayload(Request $request): array
    {
        return [
            'user_id' => auth()->id(),

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

    public function index()
    {
        $addresses = auth()->user()->addresses()->latest()->get();

        return view('shop.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
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

        Address::create($this->addressPayload($request));

        return redirect()->route('shop.addresses.index')
            ->with('success', 'Dirección guardada correctamente.');
    }

    public function edit($id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('shop.edit_address', compact('address'));
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

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

        $address->update($this->addressPayload($request));

        return redirect()->route('shop.addresses.index')
            ->with('success', 'Dirección actualizada correctamente.');
    }

    public function destroy($id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $address->delete();

        return redirect()->route('shop.addresses.index')
            ->with('success', 'Dirección eliminada correctamente.');
    }
}