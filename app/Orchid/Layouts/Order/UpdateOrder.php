<?php

namespace App\Orchid\Layouts\Order;

use App\Models\User;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UpdateOrder extends Rows
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
            Input::make('order.id')->type('hidden'),
            Group::make([
                Input::make('order.customer')->required()->title('Имя клиента'),
                Input::make('order.phone')
                    ->mask('(999) 999-9999')
                    ->title('Номер телефона')
            ]),
            Select::make('order.type')
                ->options([
                    'online'   => 'Онлайн',
                    'offline' => 'Офлайн',
                ])->title('Тип'),
            Relation::make('order.user_id')->fromModel(User::class, 'name')->title('Менеджер')->required(),
                    Select::make('order.status')
                        ->options([
                            'active'   => 'активен',
                            'completed' => 'завершен',
                            'canceled' => 'отменен',
                        ])
                        ->help('Статус заказа')
                        ->title('Статус'),
        ];
    }
}
