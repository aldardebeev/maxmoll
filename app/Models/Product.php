<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use HasFactory;
    use AsSource;
    use Chartable;
    protected $table = 'products';
    protected $fillable = ['name', 'price', 'stock'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'order_items');
    }
}
