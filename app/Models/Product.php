<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'category' => 'product',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image',
    ];
}
