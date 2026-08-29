<?php
namespace App\Modules\Academic\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ReportCardRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'academic_class_id' => 'required|exists:classes,id',
            'total_sick' => 'nullable|integer|min:0',
            'total_permission' => 'nullable|integer|min:0',
            'total_absent' => 'nullable|integer|min:0',
            'homeroom_notes' => 'nullable|string',
            'is_locked' => 'boolean'
        ];
    }
}
