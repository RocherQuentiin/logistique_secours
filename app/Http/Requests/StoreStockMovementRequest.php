<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust when auth is added
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required','integer','exists:products,id'],
            'quantity' => ['required','integer'],
            'location_id' => ['nullable','integer','exists:locations,id'],
            'batch_id' => ['nullable','integer','exists:batches,id'],
            'notes' => ['nullable','string','max:1000'],
        ];
    }
}
