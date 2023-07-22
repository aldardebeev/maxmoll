<?php

namespace App\Orchid\Layouts\ProductManagement;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class WarehousesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'warehouses';

    protected $title = 'Список складов';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID склада'),
            TD::make('name', 'Название склада'),

        ];
    }
}
