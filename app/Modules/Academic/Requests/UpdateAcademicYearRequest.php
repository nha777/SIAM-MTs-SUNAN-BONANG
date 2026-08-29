<?php

namespace App\Modules\Academic\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicYearRequest extends FormRequest
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
        $academicYear = $this->route('academic_year');
        $academicYearId = is_object($academicYear) ? $academicYear->id : $academicYear;

        return [
            'name' => 'required|string|max:9|regex:/^[0-9]{4}\/[0-9]{4}$/|unique:academic_years,active_name,' . ($academicYearId ?? 'NULL'),
            'is_active' => 'nullable|boolean',
        ];
    }
}
