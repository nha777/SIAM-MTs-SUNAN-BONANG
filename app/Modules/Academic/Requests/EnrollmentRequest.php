<?php
namespace App\Modules\Academic\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'academic_class_id' => 'required|exists:classes,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|string',
        ];
    }
}
