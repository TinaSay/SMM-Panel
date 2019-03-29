<?php

namespace App\Modules\Bosslike\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class InfoSaveRequest
 * @package App\Modules\Bosslike\Requests
 */
class InfoSaveRequest extends FormRequest
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
            'first_name' => 'required|string|min:3|max:200',
            'gender' => 'required',
        ];

    }
}
