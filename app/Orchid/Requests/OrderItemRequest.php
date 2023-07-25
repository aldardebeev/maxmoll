<?php

namespace App\Orchid\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest
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
            'order_id' => ['required'], // The order ID is required
            'product_id' => ['required'], // The product ID is required
            'count' => [
                'required',
                'integer',
                'min:1',
                // Custom validation rule to check 'count' against 'stock'
                function ($attribute, $value, $fail) {
                    $productId = $this->input('product_id');
                    $product = Product::find($productId);
                    if ($value > $product->stock) {
                        $fail('Количество продукта превышает доступное количество на складе. Товара осталось: ' . $product->stock);
                    }
                },
            ],
            'discount' => ['nullable', 'numeric', 'max:100', 'min:0'], // The discount, if provided, must be numeric and between 0 and 100
            'cost' => [], // The cost field is not explicitly validated here
        ];
    }

    /**
     * Get the validation data and calculate the 'cost' field based on other inputs.
     *
     * @return array
     */
    public function validationData()
    {
        // Get all the request data
        $data = $this->all();

        // Calculate the cost based on 'count', product price, and discount
        $count = $this->input('count');
        $price = $this->getProductPrice();
        $discount = $this->input('discount') ?? 0; // If discount is not provided, default to 0

        $cost = round($count * $price * (1 - ($discount / 100)), 2);
        $data['cost'] = $cost;
        $data['discount'] = $discount;

        // Return the modified data with 'cost' and 'discount' fields
        return $data;
    }

    /**
     * Get the price of the product based on product_id.
     *
     * @return float
     */
    private function getProductPrice(): float
    {
        // Get the product ID from the request input
        $productId = $this->input('product_id');

        // Find the product based on the ID
        $product = Product::where('id', $productId)->first();

        // Return the product price if found, or 0 if the product is not found
        return $product ? $product->price : 0.0;
    }
}
