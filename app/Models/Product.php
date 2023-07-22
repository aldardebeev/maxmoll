<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use Orchid\Filters\Filterable;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use HasFactory;
    use AsSource;
    use Chartable;
    use Filterable;
    protected $table = 'products';
    protected $fillable = ['name', 'price', 'stock'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'order_items');
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'product_movements')
            ->withPivot('quantity', 'movement_type', 'created_at');
    }

    public function productMovements()
    {
        return $this->hasMany(ProductMovement::class, 'product_id');
    }

    protected $allowedSorts = [
      'name'
    ];
}
