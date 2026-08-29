<?php
namespace App\Modules\Academic\Requests;
use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'academic_class_id' => 'required|exists:classes,id',
            'assignment_score' => 'nullable|numeric|min:0|max:100',
            'mid_exam_score' => 'nullable|numeric|min:0|max:100',
            'final_exam_score' => 'nullable|numeric|min:0|max:100',
            'final_score' => 'nullable|numeric|min:0|max:100',
            'predicate' => 'nullable|string|max:5',
            'notes' => 'nullable|string'
        ];
    }
}
