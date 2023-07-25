<?php

namespace App\Orchid\Screens\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Orchid\Layouts\Order\OrderItemListTable;
use App\Orchid\Requests\OrderItemRequest;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OrderEditScreen extends Screen
{
    public $order;
    public $orderItem;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Order $order
     * @return array
     */
    public function query(Order $order): iterable
    {
        // Retrieve the order and order items related to the order from the database
        $this->order = $order;
        $this->orderItem = OrderItem::where('order_id', $this->order->id)->get();

        // Return the order items to be displayed on the screen
        return [
            'order_items' => $this->orderItem
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
        return 'Заказ';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        // Define action buttons in the command bar, including the "Добавление товара" modal toggle
        return [
            ModalToggle::make('Добавление товара')->modal('editOrder', )->method('addOrderItem'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        // Define the layout elements for the screen, including the "OrderItemListTable" and the "Добавление товара" modal
        return [
            OrderItemListTable::class,
            Layout::modal('editOrder', Layout::rows([
                Input::make('order_id' )->type('hidden')->value($this->order->id),
                Relation::make('product_id')->fromModel(Product::class, 'name')->title('Товар')->required(),
                Input::make('count')->type('number')->title('Колличество'),
                Input::make('discount')->type('name')->title('Скидка'),
            ]))->title('Добавление товара'),
        ];

    }

    /**
     * Handle the addition of an order item.
     *
     * @param OrderItemRequest $request
     */
    public function addOrderItem(OrderItemRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Find the order related to the request
        $order = Order::where('id', $validatedData['order_id'])->first();

        // Check if the order is active before adding the order item
        if ($order->status === 'active') {
            // Find the product related to the request
            $product = Product::findOrFail($validatedData['product_id']);

            // Update the product stock and create the order item within a transaction
            DB::transaction(function () use ($product, $validatedData) {
                $product->stock -= $validatedData['count'];
                $product->save();
                OrderItem::create($validatedData);
            });
        } else {
            // Show an alert if the order is not active
            Alert::info('Заказ не активный');
            return back();
        }
    }

    /**
     * Handle the removal of an order item.
     *
     * @param int $order_item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeOrderItem(int $order_item_id)
    {
        // Find the order item to be removed
        $orderItem = OrderItem::find($order_item_id);

        if ($orderItem) {
            // Find the product related to the order item
            $product = Product::findOrFail($orderItem->product_id);

            // Update the product stock and delete the order item within a transaction
            DB::transaction(function () use ($product, $orderItem) {
                $product->stock += $orderItem->count;
                $product->save();
                $orderItem->delete();
            });

            // Show an alert after successful deletion
            Alert::info('Запись успешно удалена');
        } else {
            // Show an alert if the order item is not found
            Alert::info('Запись не найдена');
        }

        return back();
    }
}
