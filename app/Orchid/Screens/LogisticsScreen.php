<?php

namespace App\Orchid\Screens;

use App\Models\OrderItem;
use App\Orchid\Requests\ProductManagementRequest;
use App\Models\Product;
use App\Models\ProductMovement;
use App\Models\Warehouse;
use App\Orchid\Layouts\ProductManagement\ArrivalManagement;
use App\Orchid\Layouts\ProductManagement\ProductManagementTable;
use App\Orchid\Layouts\ProductManagement\ProductsTable;
use App\Orchid\Layouts\ProductManagement\SaleManagement;
use App\Orchid\Layouts\ProductManagement\TransferManagement;
use App\Orchid\Layouts\ProductManagement\WarehousesTable;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class LogisticsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'ProductMovement' => ProductMovement::filters()->paginate(10),
            'warehouses' => Warehouse::paginate(10),
            'products' => Product::filters()->defaultSort('id')->paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Логистика';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Перенос товаров')->modal('transfer')->method('transfer'),
            ModalToggle::make('Прибытие товаров')->modal('arrival')->method('arrival'),
            ModalToggle::make('Продажа со склада')->modal('sale')->method('sale'),
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
            Layout::modal('transfer', TransferManagement::class)->title('Перенос товара')->applyButton('Перенести'),
            Layout::modal('arrival', ArrivalManagement::class)->title('Прибытие товара')->applyButton('Применить'),
            Layout::modal('sale', SaleManagement::class)->title('Продажа со склада')->applyButton('Применить'),
            Layout::columns([
                WarehousesTable::class,
                ProductsTable::class
            ]),
            ProductManagementTable::class,

        ];
    }

    public function transfer(ProductManagementRequest $request)
    {
        $validatedData = $request->validated();
        $product = ProductMovement::where('product_id', $validatedData['product_id'])
            ->where('warehouse_to_id', $validatedData['warehouse_from_id'])
            ->first();
        // Проверяем, существует ли товар с указанным ID на складе
        if (!$product) {
            Toast::error('Товар не найден на указанном складе');
            return;
        }
        ProductMovement::create($validatedData);
        Toast::info('Товары перенесены');
    }

    public function arrival(ProductManagementRequest $request)
    {
        $validatedData = $request->validated();
        $product = Product::findOrFail($validatedData['product_id']);
        if ($product->stock >= $validatedData['quantity']) {
            $product->stock += $validatedData['quantity'];
            DB::transaction(function () use ($product, $validatedData) {
                $product->save();
                ProductMovement::create($validatedData);
            });
        }
    }

    public function sale(ProductManagementRequest $request)
    {
        $validatedData = $request->validated();
        $product = ProductMovement::where('product_id', $validatedData['product_id'])
            ->where('warehouse_to_id', $validatedData['warehouse_from_id'])
            ->first();
        // Проверяем, существует ли товар с указанным ID на складе

        if (!$product) {
            Toast::error('Товар не найден на указанном складе');
            return;
        }
        if ($product->quantity < $validatedData['quantity']){
            Toast::error('Товар выбранно слишком много. На складе: ' . $product->quantity);
            return;
        }

        $product = Product::findOrFail($validatedData['product_id']);
        if ($product->stock >= $validatedData['quantity']) {
            $product->stock -= $validatedData['quantity'];
            DB::transaction(function () use ($product, $validatedData) {
                $product->save();
                ProductMovement::create($validatedData);
            });
        }
    }


}
