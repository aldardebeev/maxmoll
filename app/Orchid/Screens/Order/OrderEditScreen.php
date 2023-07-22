<?php

namespace App\Orchid\Screens\Order;

use App\Http\Requests\OrderItemRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Orchid\Layouts\Order\OrderItemListTable;
use App\Orchid\Layouts\Order\OrderListTable;
use App\Orchid\Layouts\UpdateOrder;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OrderEditScreen extends Screen
{
    public $order;
    public $orderItem;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Order $order): iterable
    {
        $this->order = $order;
        $this->orderItem = OrderItem::where('order_id',   $this->order->id)->get();
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
        $name = 'Заказ №' . $this->order->id;
        return $name ;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавление товара')->modal('editOrder', )->method('create'),
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
            OrderItemListTable::class,
            Layout::modal('editOrder', Layout::rows([
                Input::make('order_id' )->type('hidden')->value($this->order->id),
                Relation::make('product_id')->fromModel(Product::class, 'name')->title('Товар')->required(),
                Input::make('count')->type('number')->title('Колличество'),
                Input::make('discount')->type('name')->title('Скидка'),
            ]))->title('Добавление товара'),
        ];

    }

    public function create(OrderItemRequest $request)
    {
        $validatedData = $request->validated();
        $product = Product::findOrFail($validatedData['product_id']);
        if ($product->stock >= $validatedData['count']) {
            $product->stock -= $validatedData['count'];
            $product->save();
            OrderItem::create($validatedData);
        }
    }

    public function removeOrderItem (int $order_item_id)
    {
        $orderItem = OrderItem::find($order_item_id);

        $product = Product::findOrFail($orderItem->product_id);
        $product->stock += $orderItem->count;
        $product->save();
        $orderItem->delete();
        Alert::info('Запись успешно удалена');
        return back();
    }



}
