<?php

namespace App\Modules\Student\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuardianRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|integer|exists:users,id',
            'guardian_name' => 'required|string|max:150',
            'guardian_relation' => 'required|string|in:ayah,ibu,paman_bibi,kakek_nenek,lainnya',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
        ];
    }
}
