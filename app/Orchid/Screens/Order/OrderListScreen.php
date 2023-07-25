<?php

namespace App\Orchid\Screens\Order;

use App\Events\OrderCreatedOrUpdated;
use App\Orchid\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Orchid\Layouts\Order\CreateOrder;
use App\Orchid\Layouts\Order\OrderListTable;
use App\Orchid\Layouts\Order\UpdateOrder;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


class OrderListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'orders' => Order::all(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {

        return 'Заказы';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Создать заказ')->modal('createOrder')->method('createOrUpdateOrder')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            OrderListTable::class,
            Layout::modal('createOrder', CreateOrder::class)->title('Создание заказа')->applyButton('Создать'),
            Layout::modal('editOrder', UpdateOrder::class)->async('asyncGetOrder'),
        ];
    }

    public function asyncGetOrder(Order $order)
    {
        return [
            'order' => $order
        ];
    }
    public function createOrUpdateOrder(OrderRequest $request): void
    {
        $orderId = $request->input('order.id');
        event(new OrderCreatedOrUpdated($orderId, $request['order']));

        Order::updateOrCreate([
            'id' => $orderId
        ], array_merge($request['order']));
        is_null($orderId) ?  Toast::info('Заказ успешно создан') : Toast::info('Заказ успешно изменен');

    }

}
