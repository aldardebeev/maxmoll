<?php

namespace App\Listeners;

use App\Events\OrderCreatedOrUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderCreatedOrUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedOrUpdated $event): void
    {
        $orderId = $event->orderId;
        $orderData = $event->orderData;

        $this->checkOrderStatus($orderId, $orderData);
//        $this->updateProductStockBasedOnOrderStatus($event->orderId, $event->orderData['status']);
    }

    private function checkOrderStatus(?int $orderId, array $orderData): void
    {
        if ($orderId !== null) {
            $oldOrder = Order::find($orderId);

            if ($oldOrder === null || $oldOrder->status === $orderData['status']) {
                return;
            }
            if ($oldOrder->status === 'completed' && $orderData['status'] === 'active'){
                return;
            }
            if ($oldOrder->status === 'active' && $orderData['status'] === 'completed') {
                return;
            }
            $this->updateProductStockBasedOnOrderStatus($orderId, $orderData['status']);
        }
    }

    private function updateProductStockBasedOnOrderStatus(int $orderId, string $status): void
    {
        if ($orderId === null) {
            return;
        }
        $orderItems = OrderItem::where('order_id', $orderId)->get();

        if ($status === 'canceled') {
            foreach ($orderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->stock += $orderItem->count;
                    $product->save();
                }
            }
        } elseif ($status === 'active' || $status === 'completed') {
            foreach ($orderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                $availableStock = $product->stock - $orderItem->count;
                if ($availableStock >= 0) {
                    $product->stock = $availableStock;
                    $product->save();
                }
            }
        }
    }
}
