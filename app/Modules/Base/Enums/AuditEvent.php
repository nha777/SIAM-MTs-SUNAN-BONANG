<?php

namespace App\Modules\Base\Enums;

enum AuditEvent: string
{
    // Domain Student & Guardian
    case STUDENT_CREATED = 'student_created';
    case STUDENT_UPDATED = 'student_updated';
    case STUDENT_DELETED = 'student_deleted';
    case STUDENT_RESTORED = 'student_restored';
    case STUDENT_GRADUATED = 'student_graduated';
    case STUDENT_TRANSFERRED = 'student_transferred';
    case STUDENT_SUSPENDED = 'student_suspended';
    
    case GUARDIAN_CREATED = 'guardian_created';
    case GUARDIAN_UPDATED = 'guardian_updated';
    case GUARDIAN_DELETED = 'guardian_deleted';
    case GUARDIAN_RESTORED = 'guardian_restored';
    
    // Domain Academic
    case ACADEMIC_YEAR_CREATED = 'academic_year_created';
    case ACADEMIC_YEAR_UPDATED = 'academic_year_updated';
    case ACADEMIC_YEAR_ACTIVATED = 'academic_year_activated';
    case ACADEMIC_YEAR_DELETED = 'academic_year_deleted';
    
    case SEMESTER_CREATED = 'semester_created';
    case SEMESTER_UPDATED = 'semester_updated';
    case SEMESTER_ACTIVATED = 'semester_activated';
    case SEMESTER_DELETED = 'semester_deleted';
    
    case ACADEMIC_CLASS_CREATED = 'academic_class_created';
    case ACADEMIC_CLASS_UPDATED = 'academic_class_updated';
    case ACADEMIC_CLASS_DELETED = 'academic_class_deleted';
    
    // Domain Authentication
    case LOGIN_SUCCESS = 'login_success';
    case LOGIN_FAILED = 'login_failed';
    case LOGOUT = 'logout';
    
    // Domain User
    case USER_CREATED = 'user_created';
    case USER_UPDATED = 'user_updated';
    case USER_DELETED = 'user_deleted';
}
