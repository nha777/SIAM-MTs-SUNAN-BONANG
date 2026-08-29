<?php

namespace App\Modules\Employee\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('employee');

        return [
            'nip' => 'nullable|string|max:255|unique:employees,nip,' . $id,
            'nuptk' => 'nullable|string|max:255|unique:employees,nuptk,' . $id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'position' => 'required|string|max:255',
            'is_active' => 'boolean',
            'join_date' => 'nullable|date',
            'create_user' => 'nullable|boolean',
        ];
    }
}
