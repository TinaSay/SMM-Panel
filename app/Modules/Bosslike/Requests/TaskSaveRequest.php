<?php

namespace App\Modules\Bosslike\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TaskSaveRequest
 * @package Modules\Bosslike\Requests
 */
class TaskSaveRequest extends FormRequest
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
        if ($this->isMethod('POST')) {
            return [
                'service_id' => 'required|exists:services,id',
                'link' => 'required|url',
                'points' => 'required|numeric|min:10',
                'amount' => 'required|numeric|min:10',
            ];
        } else {
            return [
                'points' => 'required|numeric|min:10',
                'amount' => 'required|numeric|min:10'
            ];
        }


    }
}
