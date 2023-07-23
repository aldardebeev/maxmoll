<?php

namespace App\Orchid\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'order.status' => ['sometimes', 'string', 'nullable'],
        ];
    }

    protected function prepareForValidation()
    {
        if (!isset($this->order['status'])) {
            $this->merge(['order' => array_merge($this->order, ['status' => 'active'])]);
        }
    }
}
