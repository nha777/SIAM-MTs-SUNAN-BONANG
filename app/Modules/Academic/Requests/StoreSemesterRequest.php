<?php

namespace App\Modules\Academic\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSemesterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (!isset($data['semester']) && isset($data['name'])) {
            $data['semester'] = strtolower((string) $data['name']);
        }

        if (isset($data['semester']) && is_string($data['semester'])) {
            $data['semester'] = strtolower(trim($data['semester']));
        }

        if (!isset($data['name']) && isset($data['semester'])) {
            $data['name'] = ucfirst((string) $data['semester']);
        }

        $this->replace($data);
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     */
    public function rules(): array
    {
        return [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'semester' => ['required', 'string', Rule::in(['ganjil', 'genap'])],
            'is_active' => 'nullable|boolean',
        ];
    }
}
