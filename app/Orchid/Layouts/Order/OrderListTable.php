<?php

namespace App\Orchid\Layouts\Order;

use App\Models\Order;
use App\Models\OrderItem;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;


class OrderListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'orders';

    /**
     * @return TD[]
     */
    protected function columns() : array
    {
        return [
            TD::make('customer', 'Клиент')->render(function (Order $order) {
                return '<a href="' . route('platform.order.show', ['order' => $order->id]) . '">' . $order->customer . '</a>';
            }),
            TD::make('phone' , 'Телефон'),
            TD::make('type' , 'Тип')->render(function (Order $order){
                if ($order->type === "online") {
                    return 'Онлайн';
                } else {
                    return 'Офлайн';
                }
            }),
            TD::make('status', 'Статус')->render(function (Order $order) {
                if ($order->status === "active") {
                    return 'активен';
                } elseif ($order->status === "completed") {
                    return 'завершен';
                } elseif ($order->status === "canceled") {
                    return 'отменен';
                } else {
                    return 'неизвестный статус';
                }
            }),
            TD::make('')->render(function (Order $order){
                return ModalToggle::make('Редактировать')
                    ->modal('editOrder')
                    ->method('createOrUpdateOrder')
                    ->modalTitle('Редактирование заказа' .' '. $order->customer)
                    ->asyncParameters([
                        'order' => $order->id
                    ]);
            }),

            TD::make( '')->render(function (Order $order) {
                return '<a href="' . route('platform.order.show', ['order' => $order->id]) . '">' . "Добавить Товары" . '</a>';
            }),
//            TD::make('')->render(function (Order $order) {
//                return Button::make('Удалить')
//                    ->confirm('Вы уверены, что хотите удалить заказ?')
//                    ->method('removeOrder')
//                    ->parameters(['order' => $order->id])
//                    ->icon('trash');
//            }),

        ];
    }
}
