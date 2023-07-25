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
        // Retrieve all orders from the database to display on the screen
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
        // Screen name displayed in the header
        return 'Заказы';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        // Define action buttons in the command bar, including the "Create Order" modal toggle
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
        // Define the layout elements for the screen, including the "Order List" table and modals for creating and updating orders
        return [
            OrderListTable::class,
            Layout::modal('createOrder', CreateOrder::class)->title('Создание заказа')->applyButton('Создать'),
            Layout::modal('editOrder', UpdateOrder::class)->async('asyncGetOrder'),
        ];
    }

    /**
     * Get the order data asynchronously.
     *
     * @param Order $order
     * @return array
     */
    public function asyncGetOrder(Order $order)
    {
        // Return the order data asynchronously
        return [
            'order' => $order
        ];
    }

    /**
     * Create or update an order.
     *
     * @param OrderRequest $request
     */
    public function createOrUpdateOrder(OrderRequest $request): void
    {
        // Retrieve the order ID from the request
        $orderId = $request->input('order.id');

        // Fire the "OrderCreatedOrUpdated" event with the order ID and order data
        event(new OrderCreatedOrUpdated($orderId, $request['order']));

        // Update or create the order based on the request data
        Order::updateOrCreate([
            'id' => $orderId
        ], array_merge($request['order']));

        // Show a toast message based on whether the order was created or updated
        is_null($orderId) ?  Toast::info('Заказ успешно создан') : Toast::info('Заказ успешно изменен');
    }
}
