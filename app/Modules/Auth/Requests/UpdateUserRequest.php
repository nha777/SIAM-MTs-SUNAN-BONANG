<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use App\Modules\Auth\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = User::find($this->route('user'));
        if (!$user) return true;
        return Gate::allows('user.update', $user);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user'))
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'is_active' => 'boolean',
        ];
    }
}
