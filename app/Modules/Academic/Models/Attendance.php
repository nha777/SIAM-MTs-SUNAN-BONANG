<?php
namespace App\Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Modules\Student\Models\Student;

class Attendance extends Model 
{
    use HasUuids;
    
    protected $fillable = ['student_id', 'academic_year_id', 'semester_id', 'academic_class_id', 'date', 'status', 'notes'];
    
    protected $casts = [
        'date' => 'date'
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function academicClass() { return $this->belongsTo(AcademicClass::class); }
}
