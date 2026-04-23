<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;

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

    public function store(Request $request)
    {
        $request->validate([
            'item_id'              => 'required|exists:items,id',
            'quantity'             => 'required|integer|min:1',
            'price'                => 'required|numeric',
            'total_price'          => 'required|numeric',
        ]);

        // ✅ one line — find active cart or create it
        $cart = Cart::firstOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'active']
        );

        // ✅ no duplicate rows — adds quantity if item already in cart
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
