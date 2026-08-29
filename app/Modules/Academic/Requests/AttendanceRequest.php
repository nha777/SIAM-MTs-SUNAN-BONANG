<?php
namespace App\Modules\Academic\Requests;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'academic_class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'status' => 'required|in:Hadir,Sakit,Izin,Alpa',
            'notes' => 'nullable|string'
        ];
    }
}
