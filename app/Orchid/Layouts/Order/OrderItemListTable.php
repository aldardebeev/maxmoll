<?php

namespace App\Orchid\Layouts\Order;

use App\Models\OrderItem;
use App\Models\Product;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;

class OrderItemListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'order_items';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('product_id', 'Товар')->render(function (OrderItem $orderItem){
                return $orderItem->product()->first()->name;
            }),
            TD::make('price', 'Цена')->render(function (OrderItem $orderItem){
                return $orderItem->product()->first()->price;
            }),
            TD::make('count' , 'Колличество'),
            TD::make('discount', 'Скидка'),
            TD::make('cost', 'Итоговая Цена'),
            TD::make('')->render(function (OrderItem $orderItem) {
                return Button::make('Удалить')
                    ->confirm('Вы уверены, что хотите удалить этот товар?')
                    ->method('removeOrderItem')
                    ->parameters(['order_item_id' => $orderItem->id])
                    ->icon('trash');
            }),

        ];
    }
}
