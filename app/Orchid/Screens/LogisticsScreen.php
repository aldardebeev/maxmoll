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
        // Retrieve data for the screen
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
        // Screen name displayed in the header
        return 'Логистика';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        // Define action buttons in the command bar
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
        // Define layout elements for the screen
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

    /**
     * Handle the transfer of products.
     *
     * @param ProductManagementRequest $request
     */
    public function transfer(ProductManagementRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Find the product movement record for the given product ID and warehouse ID
        $productMovement = ProductMovement::where('product_id', $validatedData['product_id'])
            ->where('warehouse_to_id', $validatedData['warehouse_from_id'])
            ->first();

        // Check if the product exists in the specified warehouse
        if (!$productMovement) {
            Toast::error('Товар не найден на указанном складе');
            return;
        }

        // Check if the requested quantity is available in the warehouse
        if ($productMovement->quantity < $validatedData['quantity']) {
            Toast::error('Товара выбрано слишком много. На складе: ' . $productMovement->quantity);
            return;
        }

        // Reduce the quantity from the current warehouse and create a new product movement record
        $productMovement->quantity -= $validatedData['quantity'];
        $productMovement->save();
        ProductMovement::create($validatedData);

        Toast::info('Товары перенесены');
    }

    /**
     * Handle the arrival of products.
     *
     * @param ProductManagementRequest $request
     */
    public function arrival(ProductManagementRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Create a new product movement record for the arrival of products
        ProductMovement::create($validatedData);
    }

    /**
     * Handle the sale of products.
     *
     * @param ProductManagementRequest $request
     */
    public function sale(ProductManagementRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();

        // Find the product movement record for the given product ID and warehouse ID
        $productMovement = ProductMovement::where('product_id', $validatedData['product_id'])
            ->where('warehouse_to_id', $validatedData['warehouse_from_id'])
            ->first();

        // Check if the product exists in the specified warehouse
        if (!$productMovement) {
            Toast::error('Товар не найден на указанном складе');
            return;
        }

        $availableQuantity = $productMovement->quantity;

        // Check if the requested quantity is available in the warehouse
        if ($availableQuantity < $validatedData['quantity']) {
            Toast::error('Товара выбрано слишком много. На складе: ' . $availableQuantity);
            return;
        }

        $product = $productMovement->product;

        // Perform the sale transaction
        if ($productMovement->quantity >= $validatedData['quantity']) {
            $productMovement->quantity -= $validatedData['quantity'];

            DB::transaction(function () use ($product, $validatedData, $productMovement) {
                $productMovement->save();
                $product->productMovements()->create($validatedData);
            });
        }
    }
}
