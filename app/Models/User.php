<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;
use Orchid\Screen\AsSource;

class User extends Authenticatable
{
    use Notifiable;
    use AsSource;

    protected $fillable = [
        'name',
    ];


}
