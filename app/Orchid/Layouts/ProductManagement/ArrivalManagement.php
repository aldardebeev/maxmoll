<?php

namespace App\Orchid\Layouts\ProductManagement;

use App\Models\Product;
use App\Models\Warehouse;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class ArrivalManagement extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            Input::make('movement_type' )->type('hidden')->value('arrival'),
            Group::make([
                Relation::make('product_id')->fromModel(Product::class, 'name')->title('Товар')->required(),
                Input::make('quantity')->type('number')->title('Колличество')->required(),
            ]),
            Relation::make('warehouse_to_id')->fromModel(Warehouse::class, 'name')->title('В склад')->required(),
        ];
    }
}
