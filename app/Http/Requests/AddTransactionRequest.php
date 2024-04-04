<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTransactionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'transaction_item' => 'required|array',
            'transaction_item.*.item_id' => 'required|exists:items_db,id',
            // 'transaction_item.*.quantity' => 'required|integer|min:1',
            // 'description' => 'required|string',
            'image_link' => 'required|string',
            'courier_name' => 'required|string',
            'ship_from' => 'required|string',
            'ship_to' => 'required|exists:users,id',
            
        ];
    }
}
