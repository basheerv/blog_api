<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'status',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class);
    }

}
