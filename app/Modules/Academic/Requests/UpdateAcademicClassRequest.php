<?php

namespace App\Modules\Academic\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use App\Modules\Academic\Models\AcademicClass;

class UpdateAcademicClassRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        $class = AcademicClass::find($this->route('class'));
        
        // If not found, let it pass to controller so it can return 404.
        // Otherwise, it would return 403 Forbidden.
        if (!$class) {
            return true;
        }

        return Gate::allows('update', $class);
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     */
    public function rules(): array
    {
        return [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('classes')->where(function ($query) {
                    return $query->where('academic_year_id', $this->academic_year_id)
                                 ->where('grade', $this->grade)
                                 ->whereNull('deleted_at');
                })->ignore($this->route('class'))
            ],
            'grade' => 'required|integer|in:7,8,9',
            'capacity' => 'required|integer|min:1',
            'display_order' => 'nullable|integer',
        ];
    }
}
