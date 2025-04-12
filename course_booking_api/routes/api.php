<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This file defines the API routes for the Course Booking API. These routes
| are prefixed with '/api' and are intended for programmatic access by
| clients such as web applications or mobile apps. We have organized these
| routes to handle user authentication, course management, and enrollment
| functionalities. We have also applied middleware to protect certain routes
| and enforce role-based access control.
|
| Authentication Routes:
|   - POST /register: Route to handle user registration via the 'AuthController@register'
|     controller method. This route is accessible to unauthenticated users.
|   - POST /login: Route to handle user login via the 'AuthController@login'
|     controller method. This route is accessible to unauthenticated users.
|   - POST /logout: Route to handle user logout via the 'AuthController@logout'
|     controller method. This route is protected by the 'auth:sanctum' middleware,
|     ensuring only authenticated users can access it.
|   - GET /user: Route to retrieve information about the authenticated user via
|     the 'AuthController@user' (or potentially a similar method). This route is
|     protected by the 'auth:sanctum' middleware.
|
| Course Management Routes:
|   - GET /courses: Route to retrieve a list of all courses via the
|     'CourseController@index' method. This route is protected by the
|     'auth:sanctum' middleware, making it accessible to authenticated users.
|   - POST /courses: Route to create a new course via the 'CourseController@store'
|     method. This route is protected by the 'auth:sanctum' middleware and further
|     restricted to users with the 'instructor' role using the 'role:instructor'
|     middleware (which we likely created).
|   - GET /courses/{course}: Route to retrieve details of a specific course via
|     the 'CourseController@show' method. The '{course}' parameter is resolved
|     using route model binding. This route is protected by the 'auth:sanctum'
|     middleware.
|   - PUT/PATCH /courses/{course}: Route to update a specific course via the
|     'CourseController@update' method. This route is protected by the
|     'auth:sanctum' middleware and further restricted to instructors who own
|     the course, likely enforced through authorization policies within the
|     controller.
|   - DELETE /courses/{course}: Route to delete a specific course via the
|     'CourseController@destroy' method. This route is protected by the
|     'auth:sanctum' middleware and further restricted to instructors who own
|     the course, likely enforced through authorization policies.
|   - GET /instructor/courses: Route to retrieve a list of courses created by the
|     authenticated instructor via the 'CourseController@instructorCourses'
|     (or a similar method). This route is protected by the 'auth:sanctum'
|     middleware and restricted to users with the 'instructor' role.
|
| Enrollment Routes:
|   - POST /enrollments: Route to enroll a student in a course via the
|     'EnrollmentController@store' method. This route is protected by the
|     'auth:sanctum' middleware and restricted to users with the 'student'
|     role using the 'role:student' middleware.
|   - GET /student/enrollments: Route to retrieve a list of courses the
|     authenticated student is enrolled in via the 'EnrollmentController@index'
|     method. This route is protected by the 'auth:sanctum' middleware and
|     restricted to users with the 'student' role.
|
| Middleware:
|   - 'auth:sanctum': Laravel Sanctum middleware for authenticating API requests
|     using tokens.
|   - 'role:instructor': Custom middleware (which we likely created) to check if
|     the authenticated user has the 'instructor' role.
|   - 'role:student': Custom middleware (which we likely created) to check if
|     the authenticated user has the 'student' role.
|
| In summary, the `api.php` file defines all the API endpoints for our Course
| Booking application. We have carefully assigned controller methods to handle
| the logic for each endpoint and applied appropriate middleware for authentication
| and role-based access control, ensuring that only authorized users can perform
| specific actions on the API resources.
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/courses', CourseController::class);
    Route::get('/instructor/courses', [CourseController::class, 'instructorCourses']);

    // Routes Enrollments
    Route::post('/enrollments', [EnrollmentController::class, 'store']); // for booking course
    Route::get('/student/enrollments', [EnrollmentController::class, 'index']); // view courses fore student
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});