<?php
/*
|--------------------------------------------------------------------------
| User.php Model
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This file defines the `User` Eloquent model, which represents users in the
| Course Booking API. It extends Laravel's base `Authenticatable` model,
| providing functionality for authentication, authorization, and interacting
| with the `users` database table.
|
| use HasApiTokens, HasFactory, Notifiable;
|   These traits are included in the `User` model to provide additional
|   functionality:
|   - `HasApiTokens`: From Laravel Sanctum, it allows the user to generate
|     and manage API tokens for authentication.
|   - `HasFactory`: Provides the ability to use factories for generating
|     dummy user data for testing and seeding.
|   - `Notifiable`: Enables the user to receive notifications (e.g., via email).
|
| protected $table = 'users';
|   Specifies the database table associated with this model, which is the
|   'users' table.
|
| protected $fillable = ['name', 'email', 'password', 'role'];
|   Defines the attributes that are mass assignable. These attributes can be
|   set in bulk using methods like `User::create()` or `$user->fill()`. We
|   allow 'name', 'email', 'password', and 'role' to be mass assigned during
|   user creation and updates.
|
| protected $hidden = ['password', 'remember_token'];
|   Defines the attributes that should be hidden from array or JSON serialization.
|   We hide 'password' for security reasons and 'remember_token' as it's typically
|   not needed in an API context.
|
| protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];
|   Defines the attribute casting. Here, 'email_verified_at' is cast to a
|   `datetime` object for easier manipulation, and 'password' is cast to a
|   `hashed` type, which automatically encrypts the password using bcrypt
|   when it's set on the model.
|
| public function courses(): HasMany
|   Defines a "has many" relationship with the `Course` model. This indicates
|   that a user (specifically an instructor) can have many courses.
|   - `return $this->hasMany(Course::class, 'instructor_id');` establishes the
|     relationship, specifying that the foreign key in the 'courses' table
|     that relates to the 'users' table is 'instructor_id'.
|   - This relationship allows us to access the courses created by a user
|     (instructor) using `$user->courses`.
|
| public function enrollments(): HasMany
|   Defines a "has many" relationship with the `Enrollment` model. This indicates
|   that a user (specifically a student) can have many enrollments.
|   - `return $this->hasMany(Enrollment::class, 'student_id');` establishes the
|     relationship, specifying that the foreign key in the 'enrollments' table
|     that relates to the 'users' table is 'student_id'.
|   - This relationship allows us to access the enrollments of a user (student)
|     using `$user->enrollments`.
|
| public function enrolledCourses(): BelongsToMany
|   Defines a "belongs to many" relationship with the `Course` model (specifically
|   representing courses the user is enrolled in). This is the inverse of the
|   `Course::students()` relationship.
|   - `return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
|             ->withTimestamps();` establishes the relationship through the
|     'enrollments' pivot table, using 'student_id' as the foreign key for the
|     `User` model and 'course_id' as the foreign key for the `Course` model.
|     `->withTimestamps()` automatically maintains timestamps in the pivot table.
|   - This relationship allows us to access the courses a user (student) is
|     enrolled in using `$user->enrolledCourses`.
|
| public function isInstructor(): bool
|   A custom helper method to check if the user has the 'instructor' role.
|   - `return $this->role === 'instructor';` returns `true` if the user's 'role'
|     attribute is 'instructor', and `false` otherwise. This is used for
|     role-based authorization.
|
| public function isStudent(): bool
|   A custom helper method to check if the user has the 'student' role.
|   - `return $this->role === 'student';` returns `true` if the user's 'role'
|     attribute is 'student', and `false` otherwise. This is used for
|     role-based authorization.
|
| In summary, the `User` model represents users in our API, handling authentication
| through Laravel Sanctum. It defines which attributes are fillable and hidden,
| casts attribute types, and establishes relationships with the `Course` and
| `Enrollment` models to represent created courses and course enrollments. The
| helper methods `isInstructor()` and `isStudent()` provide a convenient way to
| check user roles within the application's logic.
*/
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;





class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    //     'password' => 'hashed',
    // ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // public function enrolledCourses(): BelongsToMany
    // {
    //     return $this->belongsToMany(Course::class, 'enrollments')->withTimestamps();
    // }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')->withTimestamps();
    }
}


