<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

class Warehouse extends Model
{
    use HasFactory;
    use AsSource;
    use Chartable;
    protected $fillable = ['name']; // Добавьте другие заполняемые поля, если необходимо.

    public function productMovements()
    {
        return $this->hasMany(ProductMovement::class, 'warehouse_from_id')->orWhere('warehouse_to_id', $this->id);
    }
}
