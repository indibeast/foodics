<?php

namespace App\Http\Requests;

use App\Rules\IngredientAvailability;
use Illuminate\Foundation\Http\FormRequest;

class OrderPostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'products' => ['array', new IngredientAvailability],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }
}
