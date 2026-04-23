<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Requests\CartRequest;
class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    public function store(CartRequest $request)
    {

        $cart = Cart::firstOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'active']
        );

        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'item_id' => $request->item_id,
            ],
            [
                'quantity'             => \DB::raw("quantity + {$request->quantity}"),
                'price'                => $request->price,
                'special_instructions' => $request->special_instructions,
                'category_id'          => $request->category_id
            ]
        );

        return response()->json([
            'message' => 'Item added to cart',
            'data'    => $cartItem->load('item'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
