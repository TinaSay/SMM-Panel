<?php

namespace App\Modules\SmmPro\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class CategorySaveRequest
 * @package App\Http\Requests
 */
class CategorySaveRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('smmpro_categories', 'name')->ignore($this->input('name'), 'name'),
                'string',
                'min:3',
                'max:255'
            ],
            'description' => 'nullable',
            'active' => 'required|boolean',
            'alias' => 'nullable|string|alpha_dash',
            'icon' => 'nullable|image|max:10000|mimes:jpeg,png,gif',
            'parent_id' => 'nullable|numeric'
        ];
    }
}
