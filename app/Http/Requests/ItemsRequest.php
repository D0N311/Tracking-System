<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemsRequest extends FormRequest
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
            'item_name' => 'required',
            'item_type' => 'required',
            'stocks' => 'nullable',
            'model_number' => 'required',
            'image' => 'required|file|mimes:jpeg,jpg,png|max:5120',
            'under_company' => 'required|exists:company_db,id',
            'owned_by' => 'nullable|email|exists:users,email',
        ];
    }
}
