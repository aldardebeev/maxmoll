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
     * @return array
     */
    public function query(Order $order): iterable
    {
        $this->order = $order;
        $this->orderItem = OrderItem::where('order_id', $this->order->id)->get();
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
        return 'Заказ';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
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

    public function addOrderItem(OrderItemRequest $request)
    {
        $validatedData = $request->validated();
        $order = Order::where('id', $validatedData['order_id'])->first();

        if ($order->status === 'active') {
            $product = Product::findOrFail($validatedData['product_id']);
            $product->stock -= $validatedData['count'];

            // Используем транзакцию для объединения двух операций в одну
            DB::transaction(function () use ($product, $validatedData) {
                $product->save();
                OrderItem::create($validatedData);
            });
        } else {
            Alert::info('Заказ не активный');
            return back();
        }
    }

    public function removeOrderItem(int $order_item_id)
    {
        $orderItem = OrderItem::find($order_item_id);

        if ($orderItem) {
            $product = Product::findOrFail($orderItem->product_id);
            $product->stock += $orderItem->count;

            // Используем транзакцию для объединения операций удаления и обновления товара
            DB::transaction(function () use ($product, $orderItem) {
                $product->save();
                $orderItem->delete();
            });

            Alert::info('Запись успешно удалена');
        } else {
            Alert::info('Запись не найдена');
        }

        return back();
    }



}
