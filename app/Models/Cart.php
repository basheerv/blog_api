<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CartItem;
class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable = ['user_id','status'];

       /**
         * Get the user that owns the Cart
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

        /**
         * Get the user that owns the Cart
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function cartitems(): BelongsTo
        {
            return $this->hasMany(CartItem::class);
        }
}
