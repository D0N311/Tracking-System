<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCompanyRequest extends FormRequest
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
            'company_name' => 'required|string|unique:company_db,company_name',
            'description' => 'required|string',
            'location' => 'required',
            'admin_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->whereNotNull('activated_at')->where('role', '!=', 'SuperAdmin');
                }),
            ],
        ];
    }
}
