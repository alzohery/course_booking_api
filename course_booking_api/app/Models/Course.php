<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;




class Course extends Model
{
    use HasFactory;

    protected $fillable = ['instructor_id', 'title', 'description', 'max_students'];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    // public function students(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'enrollments')->withTimestamps();
    // }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')->withTimestamps();
    }


    public function enrollments(): BelongsToMany // إضافة هذه العلاقة
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')->withTimestamps();
    }
}