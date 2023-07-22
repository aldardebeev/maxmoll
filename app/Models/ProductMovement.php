<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

class ProductMovement extends Model
{
    use HasFactory;
    use AsSource;
    use Chartable;
    use Filterable;
    protected $fillable = ['product_id', 'warehouse_from_id', 'warehouse_to_id', 'quantity', 'movement_type'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouseFrom()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_from_id');
    }

    public function warehouseTo()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_to_id');
    }

    protected  $allowedSort = [
        'movement_type'
    ];




}
