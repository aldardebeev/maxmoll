<?php

namespace App\Orchid\Screens\Order;

use App\Http\Requests\OrderItemRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Order;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Orchid\Layouts\CreateOrder;
use App\Orchid\Layouts\Order\OrderListTable;
use App\Orchid\Layouts\UpdateOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Fields\Input;
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
        $this->checkStatus($request['order']);
        $orderId = $request->input('order.id');
        Order::updateOrCreate([
            'id' => $orderId
        ], array_merge($request['order']));
        is_null($orderId) ?  Toast::info('Заказ успешно создан') : Toast::info('Заказ успешно изменен');

    }

    public function checkStatus($order)
    {
        $oldOrder = Order::find($order['id']);

        if ($oldOrder === null || $oldOrder->status === $order['status'] ) {
            return;
        }
        if ($oldOrder->status === 'completed' &&  $order['status'] === 'active' || $oldOrder->status === 'active' &&  $order['status'] === 'completed'){
            return;
        }

        $orderItems = OrderItem::where('order_id', $order['id'])->get();

        if ($order['status'] === 'canceled') {
            foreach ($orderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->stock += $orderItem->count;
                    $product->save();
                }
            }
        } elseif ($order['status'] === 'active' || $order['status'] === 'completed') {
            foreach ($orderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                    $availableStock = $product->stock - $orderItem->count;
                    if ($availableStock >= 0) {
                        $product->stock = $availableStock;
                        $product->save();
                    }
            }
        } else{
            return;
        }

    }
}
