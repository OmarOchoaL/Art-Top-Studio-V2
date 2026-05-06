<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// TIENDA
Route::get('/tienda', [ShopController::class, 'index'])->name('shop.index');
Route::get('/tienda/producto/{id}', [ShopController::class, 'show'])->name('shop.show');
Route::post('/tienda/producto/{id}', [ShopController::class, 'submitProduct'])->name('shop.submit');

Route::get('/tienda/carrito', [ShopController::class, 'cart'])->name('shop.cart');
Route::delete('/tienda/carrito/{index}', [ShopController::class, 'removeFromCart'])->name('shop.cart.remove');
Route::delete('/tienda/carrito', [ShopController::class, 'clearCart'])->name('shop.cart.clear');

// REGISTRO CLIENTE
Route::get('/registro', [ClientAuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [ClientAuthController::class, 'register'])->name('register.store');

// LOGIN
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// INFORMACIÓN DE LA TIENDA
Route::view('/nosotros', 'shop.nosotros')->name('shop.nosotros');
Route::view('/contacto', 'shop.contacto')->name('shop.contacto');

// RECUPERAR PASSWORD
Route::get('/olvide-contrasena', [ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/olvide-contrasena', [ClientAuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/restablecer-contrasena/{token}', [ClientAuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/restablecer-contrasena', [ClientAuthController::class, 'resetPassword'])->name('password.update');

Route::post('/login', function () {
    request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(request(['email', 'password']))) {
        request()->session()->regenerate();

        $user = Auth::user();

        if (in_array($user->role, ['admin', 'colaborador'])) {
            return redirect()->route('inicio');
        }

        return redirect()->route('shop.index');
    }

    return back()->with('error', 'Credenciales incorrectas')->withInput();
})->name('login.store');

// LOGOUT
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | CLIENTE - DIRECCIONES / CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get('/tienda/direcciones', [AddressController::class, 'index'])->name('shop.addresses.index');
    Route::post('/tienda/direcciones', [AddressController::class, 'store'])->name('shop.addresses.store');
    Route::get('/tienda/direcciones/{id}/editar', [AddressController::class, 'edit'])->name('shop.addresses.edit');
    Route::put('/tienda/direcciones/{id}', [AddressController::class, 'update'])->name('shop.addresses.update');
    Route::delete('/tienda/direcciones/{id}', [AddressController::class, 'destroy'])->name('shop.addresses.destroy');

    Route::get('/tienda/checkout', [CheckoutController::class, 'address'])->name('shop.checkout');
    Route::post('/tienda/checkout/guardar-direccion', [CheckoutController::class, 'saveAddress'])->name('shop.checkout.saveAddress');
    Route::post('/tienda/checkout/seleccionar-direccion', [CheckoutController::class, 'selectAddress'])->name('shop.checkout.selectAddress');
    Route::get('/tienda/checkout/resumen', [CheckoutController::class, 'summary'])->name('shop.checkout.summary');
    Route::post('/tienda/checkout/pago', [ShopController::class, 'payment'])->name('shop.checkout.payment');
    Route::post('/tienda/checkout/procesar', [ShopController::class, 'processCheckout'])->name('shop.checkout.process');

    /*
    |--------------------------------------------------------------------------
    | PERFIL CLIENTE
    |--------------------------------------------------------------------------
    */

    Route::get('/tienda/perfil', [ClientAuthController::class, 'editProfile'])->name('shop.profile');
    Route::put('/tienda/perfil', [ClientAuthController::class, 'updateProfile'])->name('shop.profile.update');

    /*
    |--------------------------------------------------------------------------
    | ADMIN / COLABORADOR
    |--------------------------------------------------------------------------
    */

    // INICIO ADMIN
    Route::get('/', [PedidoController::class, 'index'])->name('inicio');

    // CATEGORÍAS
    Route::resource('categorias', CategoryController::class);

    // PRODUCTOS
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    // AJAX PRODUCTOS
    Route::get('/productos/{product}/options', function (\App\Models\Product $product) {
        return response()->json($product->options);
    });

    Route::get('/api/product/{id}', [ProductoController::class, 'show']);

    // PEDIDOS
    Route::resource('pedidos', PedidoController::class);

    // ESTADO PEDIDO
    Route::put('/pedidos/{id}/estado', [PedidoController::class, 'cambiarEstado'])
        ->name('pedidos.estado');

    // COTIZAR
    Route::post('/cotizar', [PedidoController::class, 'cotizar'])
        ->name('pedidos.cotizar');

    // PDF / TICKET
    Route::get('/pedidos/{id}/pdf', [PedidoController::class, 'pdf'])
        ->name('pedidos.pdf');

    Route::get('/pedidos/{id}/pdf-entrega', [PedidoController::class, 'pdfEntrega'])
        ->name('pedidos.pdfEntrega');

    // USUARIOS
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    Route::post('/usuarios/{id}/verify-edit', [UserController::class, 'verifyEdit'])->name('usuarios.verifyEdit');

});