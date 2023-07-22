<?php

namespace App\Orchid\Layouts\ProductManagement;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class ProductsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'products';

    protected $title = 'Список товаров';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
                TD::make('id', 'ID товара'),
                TD::make('name', 'Название товара')->filter(TD::FILTER_TEXT),
                TD::make('price', 'Цена'),
                TD::make('stock', 'Количество'),
        ];
    }
}
