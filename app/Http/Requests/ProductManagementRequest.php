<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductManagementRequest extends FormRequest
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
            'movement_type' => ['required', 'string'],
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer'],
            'warehouse_from_id' => [ 'integer'],
            'warehouse_to_id' => [ 'integer'],
        ];
    }
}
