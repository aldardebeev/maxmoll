<?php

namespace App\Orchid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductManagementRequest extends FormRequest
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
            'movement_type' => ['required', 'string'],       // The movement type is required and must be a string
            'product_id' => ['required', 'integer'],        // The product ID is required and must be an integer
            'quantity' => ['required', 'integer'],          // The quantity is required and must be an integer
            'warehouse_from_id' => ['integer'],             // The "from" warehouse ID is optional and must be an integer
            'warehouse_to_id' => ['integer'],               // The "to" warehouse ID is optional and must be an integer
        ];
    }
}
