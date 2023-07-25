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
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order.customer' => ['required', 'string'],
            'order.phone' => ['required', 'string'],
            'order.type' => ['required', 'string'],
            'order.user_id' => ['required', 'integer'],
            'order.status' => ['sometimes', 'string', 'nullable',
                function ($attribute, $value, $fail) {
                    $oldOrders = Order::where('id', $this->order['id'])->get();
                    foreach ($oldOrders as $oldOrder){
                        if($oldOrder->status === 'canceled' && $this->order['status'] !== 'canceled'){
                            $orderItems = OrderItem::where('order_id', $this->order['id'])->get();
                            foreach ($orderItems as $orderItem) {
                                $product = Product::find($orderItem->product_id);
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

    protected function prepareForValidation()
    {
        if (!isset($this->order['status'])) {
            $this->merge(['order' => array_merge($this->order, ['status' => 'active'])]);
        }


    }
}
