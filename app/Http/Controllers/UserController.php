<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->soloAdmin();

        $usuarios = User::orderBy('id', 'desc')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $this->soloAdmin();
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $this->soloAdmin();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,cliente,colaborador',
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente');
    }

    public function verifyEdit(Request $request, $id)
    {
        $this->soloAdmin();
        $request->validate([
            'admin_password' => 'required|string',
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return redirect()->route('usuarios.index')
                ->with('error', 'La contraseña del administrador es incorrecta.');
        }

        session()->put('can_edit_user_' . $id, true);

        return redirect()->route('usuarios.edit', $id);
    }

    public function edit($id)
    {
        $this->soloAdmin();
        if (!session()->pull('can_edit_user_' . $id)) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Debes confirmar tu contraseña para editar este usuario.');
        }

        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $this->soloAdmin();
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'role' => 'required|in:admin,cliente,colaborador',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (auth()->id() == $usuario->id && $request->role !== 'admin') {
            return back()
                ->withInput()
                ->with('error', 'No puedes cambiar tu propio rol de administrador.');
        }

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(Request $request, $id)
    {
        $this->soloAdmin();
        $usuario = User::findOrFail($id);

        $request->validate([
            'admin_password' => 'required|string',
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->with('error', 'La contraseña del administrador es incorrecta.');
        }

        if (auth()->id() == $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    private function soloAdmin()
{
    if (auth()->user()->role !== 'admin') {
        abort(403, 'No autorizado.');
    }
}
}