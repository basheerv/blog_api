<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $fillable = [
        'cart_id',
        'category_id',
        'item_id',
        'quantity',
        'price',
        'special_instructions'
    ];

        /**
         * Get all of the comments for the cartItem
         *
         * @return \Illuminate\Database\Eloquent\Relations\belongsTo
         */
        public function carts(): belongsTo
        {
            return $this->belongsTo(Cart::class);
        }

}
