<?php

namespace App\Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveTopicRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required_with:edit,1|numeric|exists:topics,id',
            'blog_id' => 'required|numeric|exists:blogs,id',
            'name' => 'required|string'
        ];
    }
}