<?php

namespace  App\Orchid\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest
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
            'order_id' => ['required'],
            'product_id' => ['required'],
            'count' => [
                'required',
                'integer',
                'min:1',
                // Пользовательское правило валидации для проверки 'count' по отношению к 'stock'
                function ($attribute, $value, $fail) {
                    $productId = $this->input('product_id');
                    $product = Product::find($productId);
                    if ($value > $product->stock) {
                        $fail('Количество продукта превышает доступное количество на складе. Товара осталось: '. $product->stock);
                    }
                },
            ],
            'discount' => ['nullable', 'numeric', 'max:100','min:0'],
            'cost' => [ ],
        ];
    }
    public function validationData()
    {
        $data = $this->all();

        $count = $this->input('count');
        $price = $this->getProductPrice();
        $discount = $this->input('discount') ?? 0;

        $cost = round($count * $price * (1 - ($discount / 100)), 2);
        $data['cost'] = $cost;
        $data['discount'] = $discount;
        return $data;
    }

    /**
     * Получите цену продукта на основе product_id.
     *
     * @return float
     */
    private function getProductPrice(): float
    {
        $productId = $this->input('product_id');
        $product = Product::where('id', $productId)->first();

        return $product ? $product->price : 0.0; // Если продукт не найден, вернуть 0
    }
}
