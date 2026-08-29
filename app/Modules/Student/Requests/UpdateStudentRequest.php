<?php

namespace App\Modules\Student\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
        $student = $this->route('student');
        $studentId = is_object($student) ? $student->id : $student;

        return [
            'nisn' => 'nullable|string|size:10|regex:/^[0-9]{10}$/|unique:students,active_nisn,' . ($studentId ?? 'NULL'),
            'name' => 'required|string|max:150',
            'gender' => 'required|string|in:L,P',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'status' => 'required|string|in:aktif,lulus,mutasi,keluar,skorsing',
            'guardian_id' => 'required|integer|exists:guardians,id',
            'class_id' => 'nullable|integer|exists:classes,id',
        ];
    }
}
