<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\User;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

class Order extends Model
{
    use HasFactory;
    use AsSource;
    use Chartable;
    protected $table = 'orders';

    protected $fillable = [
        'customer',
        'phone',
        'created_at',
        'completed_at',
        'user_id',
        'type',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->hasOne(OrderItem::class, 'order_id');
    }


}
