<?php

namespace App\Orchid\Layouts\ProductManagement;

use App\Models\ProductMovement;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductManagementTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'ProductMovement';

    protected $title = 'История движения товаров';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('product_id', 'ID Товарa')->render(function (ProductMovement $productMovement){
                return $productMovement->product()->first()->id;
            })->width('50'),
            TD::make('product_id', 'Товар')->render(function (ProductMovement $productMovement){
                return $productMovement->product()->first()->name;
            })->filter(TD::FILTER_TEXT),
            TD::make('quantity' , 'Количество'),
            TD::make('warehouse_from_id', 'Из склада')->render(function (ProductMovement $productMovement){
                if($productMovement->warehouseFrom()->first()){
                    return $productMovement->warehouseFrom()->first()->name;
                }
                return '______';
            }),
            TD::make('warehouse_to_id', 'В склад')->render(function (ProductMovement $productMovement){
                if($productMovement->warehouseTo()->first()){
                    return $productMovement->warehouseTo()->first()->name;
                }
                return '______';
            }),
            TD::make('movement_type' , 'Тип движения')->render(function (ProductMovement $productMovement) {
                if ($productMovement->movement_type === "transfer") {
                    return 'Перенос';
                } elseif ($productMovement->movement_type === "arrival") {
                    return 'Прибытие';
                } else {
                    return 'Продажа';
                }
            })->sort(),
        ];
    }
}
