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

            'description' => 'required|string',
            'courier_name' => 'required|string',
            'ship_from' => 'required|string|exists:company_db,company_name',
            'ship_to' => 'required|exists:users,email',
            'image' => 'required|file|mimes:jpeg,jpg,png|max:2048',
            'transaction_item' => 'required|array',
            'transaction_item.*.id' => 'required|exists:items_db,id',
            
            
        ];
    }
}
