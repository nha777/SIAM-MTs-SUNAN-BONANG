<?php

namespace App\Modules\Academic\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('subject');

        return [
            'code' => 'required|string|max:255|unique:subjects,code,' . $id,
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
