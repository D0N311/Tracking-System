<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|exists:reset_code_passwords,code',
            'password' => 'required|string|min:8',
            'c_password' => 'required|string|same:password'
        ];
    }

    /**
     * messages
     *
     * @return void
     */
    public function messages()
    {
        return [
            'code.exists' => trans('passwords.code_is_invalid')
        ];
    }
}
