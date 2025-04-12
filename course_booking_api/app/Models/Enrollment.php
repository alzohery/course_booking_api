<?php
/*
|--------------------------------------------------------------------------
| Enrollment.php Model
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This file defines the `Enrollment` Eloquent model for the Course Booking API.
| This model represents the intermediate table 'enrollments' that establishes
| the many-to-many relationship between students (Users with 'student' role)
| and Courses. It tracks which students are enrolled in which courses and
| when they enrolled.
|
| protected $table = 'enrollments';
|   Specifies the database table associated with this model, which is the
|   'enrollments' table.
|
| protected $fillable = ['student_id', 'course_id', 'enrollment_date'];
|   Defines the attributes that are mass assignable. These attributes can be
|   set in bulk using methods like `Enrollment::create()` or `$enrollment->fill()`.
|   We allow 'student_id', 'course_id', and 'enrollment_date' to be mass assigned
|   when creating new enrollment records.
|
| public function student(): BelongsTo
|   Defines a "belongs to" relationship with the `User` model, representing
|   the student who is enrolled.
|   - `return $this->belongsTo(User::class, 'student_id');` establishes the
|     relationship, specifying that the foreign key in the 'enrollments' table
|     that relates to the 'users' table is 'student_id'.
|   - This relationship allows us to access the student of an enrollment using
|     `$enrollment->student`.
|
| public function course(): BelongsTo
|   Defines a "belongs to" relationship with the `Course` model, representing
|   the course in which the student is enrolled.
|   - `return $this->belongsTo(Course::class);` establishes the relationship,
|     assuming the foreign key in the 'enrollments' table that relates to the
|     'courses' table is 'course_id' (which is the Laravel convention based on
|     the relationship name).
|   - This relationship allows us to access the course of an enrollment using
|     `$enrollment->course`.
|
| In summary, the `Enrollment` model serves as the link between students and
| courses in our API. It stores information about when a student enrolled in a
| particular course and provides relationships to easily access the associated
| student and course models. This model is crucial for managing and retrieving
| enrollment data within the application.
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Enrollment extends Model
{
    /** @use HasFactory<\Database\Factories\EnrollmentFactory> */
    use HasFactory;
    
    protected $fillable = ['student_id', 'course_id'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
