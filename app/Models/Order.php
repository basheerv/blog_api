<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'order_number',
        'user_id',
        'cart_id',
        'subtotal',
        'tax',
        'discount',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'confirmed_at',
        'delivered_at'
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Y') . str_pad(
                Order::whereYear('created_at', date('Y'))->count() + 1,
                4, '0', STR_PAD_LEFT
            );
        });
    }
}
