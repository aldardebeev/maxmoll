<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

class OrderItem extends Model
{
    use HasFactory;
    use AsSource;
    use Chartable;
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'count', 'discount', 'cost'];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}

