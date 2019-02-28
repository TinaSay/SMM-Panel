<?php

namespace App\Modules\Cart\Controllers;

use App\Http\Controllers\Controller;
use Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function ajaxGetCartContents()
    {
        return response()->json([
            'items' => Cart::getContents()
        ]);
    }

    public function ajaxAddItem(Request $request)
    {
        Cart::addItem($request->item);

        return response()->json([
            'status' => 0,
            'message' => 'Услуга добавлена в корзину'
        ]);
    }

    public function ajaxClearCart()
    {
        Cart::emptyCart();

        return response()->json([
            'status' => 0,
            'message' => 'Корзина очищена'
        ]);
    }

    public function emptyCart()
    {
        Cart::emptyCart();

        return redirect('/catalog');
    }

    public function checkout()
    {
        $cart = Cart::getContents();

        return view('cart::checkout')
            ->with(compact('cart'));
    }

    public function postCheckout()
    {
        $result = Cart::checkout();
        $redirect = null;

        switch ($result) {
            case -2:
                $this->error('У вас не хватает средств. чтобы заказать все услуги в корзине.');
            case -1:
                $this->error('У вас недостаточно средств для оформления заказа.');
                break;
            case 0:
                $this->success('Заказы по всем улугам оформлены.');
                $redirect = '/catalog';
                break;
            default:
                $this->error('Неизвестная ошибка');
                break;
        }

        return $redirect ? redirect($redirect) : redirect()->back();
    }
}