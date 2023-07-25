<?php

namespace App\Orchid\Requests;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Orchid\Support\Facades\Alert;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // The authorization logic for the request (Always returning true means all users are authorized)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Define the validation rules for the request data
        return [
            'order.customer' => ['required', 'string'], // The customer name is required and must be a string
            'order.phone' => ['required', 'string'], // The customer phone is required and must be a string
            'order.type' => ['required', 'string'], // The order type is required and must be a string
            'order.user_id' => ['required', 'integer'], // The user ID is required and must be an integer
            'order.status' => [
                'sometimes', // The status field is not required (nullable)
                'string', // The status must be a string
                'nullable', // The status can be null
                // Custom validation rule to check if the order status transition is allowed
                function ($attribute, $value, $fail) {
                    // Retrieve all old orders with the same ID
                    $oldOrders = Order::where('id', $this->order['id'])->get();
                    foreach ($oldOrders as $oldOrder) {
                        // Check if the old status is 'canceled' and the new status is not 'canceled'
                        if ($oldOrder->status === 'canceled' && $this->order['status'] !== 'canceled') {
                            // Retrieve all order items for the current order
                            $orderItems = OrderItem::where('order_id', $this->order['id'])->get();
                            foreach ($orderItems as $orderItem) {
                                // Retrieve the product for the order item
                                $product = Product::find($orderItem->product_id);
                                // Fail validation if the count in the order item exceeds the product stock
                                if ($orderItem->count > $product->stock) {
                                    $fail('Недостаточно товара на складе.');
                                }
                            }
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set a default status 'active' if the 'status' field is not set in the request
        if (!isset($this->order['status'])) {
            $this->merge(['order' => array_merge($this->order, ['status' => 'active'])]);
        }
    }
}
