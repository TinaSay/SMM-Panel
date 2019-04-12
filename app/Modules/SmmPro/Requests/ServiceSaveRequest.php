<?php

namespace App\Modules\SmmPro\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ServiceSaveRequest
 * @package App\Http\Requests
 */
class ServiceSaveRequest extends FormRequest
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
            'root_category' => 'required|numeric|exists:smmpro_categories,id',
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'quantities' => 'required',
            'service_api' => 'required|string',
            'service_order_api' => 'required|string',
            'type' => 'required|string',
            'prices' => 'required',
//            'reseller_price' => 'required',
            'active' => 'required|boolean',
        ];
    }
}
