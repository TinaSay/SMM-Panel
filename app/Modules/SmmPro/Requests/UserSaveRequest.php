<?php

namespace App\Modules\SmmPro\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UserSaveRequest
 * @package App\Http\Requests
 */
class UserSaveRequest extends FormRequest
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
            'billing_id' => 'required|numeric',
            'login' => 'required|string|max:255|min:5|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|numeric',
            'ip' => 'nullable|ip'
        ];
    }
}
