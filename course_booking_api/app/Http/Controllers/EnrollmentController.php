<?php
/*
|--------------------------------------------------------------------------
| EnrollmentController.php Actions
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This controller manages student enrollment in courses for the Course Booking API.
| It includes methods for enrolling in a course and viewing the enrolled courses
| for a student, with appropriate business logic and authorization checks.
|
| 1. store(Request $request):
|    Handles the enrollment of a student in a specific course.
|    Accessible only to authenticated users with the 'student' role.
|    Validates the incoming request data, ensuring 'course_id' is provided and exists.
|    Retrieves the authenticated student and the target course.
|    Implements business logic checks:
|      - Prevents instructors from enrolling.
|      - Prevents students from enrolling in the same course twice.
|      - Checks if the course has reached its maximum student capacity.
|    If all checks pass, creates a new enrollment record in the database,
|    linking the student to the course with the current enrollment date.
|    Returns a JSON response containing the newly created enrollment record with a 201 Created status.
|    Returns appropriate error responses (e.g., 403 Forbidden, 409 Conflict, 400 Bad Request)
|    if business rules are violated.
|
| 2. index(Request $request):
|    Retrieves and returns a list of courses that the authenticated student is enrolled in.
|    Accessible only to authenticated users with the 'student' role.
|    Retrieves the currently authenticated student.
|    Queries the database to fetch all enrollment records associated with that student,
|    eager-loading the 'course' relationship to include course details.
|    Returns a JSON response containing an array of enrollment records, each including
|    the associated course information.
|
*/
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student = $request->user();
        $courseId = $request->input('course_id');
        $course = Course::findOrFail($courseId);

        // Verify that the user is a student
        if ($student->role !== 'student') {
            return response()->json(['message' => 'Only students can enroll in courses.'], 403);
        }

        // Verify that the student has not booked the course before
        if (Enrollment::where('student_id', $student->id)->where('course_id', $courseId)->exists()) {
            return response()->json(['message' => 'You are already enrolled in this course.'], 409); // 409 Conflict
        }

        // Check that the course has not reached the maximum number of students
        if ($course->enrollments()->count() >= $course->max_students) {
            return response()->json(['message' => 'This course is full.'], 400); // 400 Bad Request
        }

        // Create a reservation record
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $courseId,
        ]);

        return response()->json(['message' => 'Successfully enrolled in the course.', 'enrollment' => $enrollment], 201);
    }

    public function index(Request $request)
    {
        $student = $request->user();

        if ($student->role !== 'student') {
            return response()->json(['message' => 'Only students can view their enrollments.'], 403);
        }

        $enrolledCourses = $student->enrolledCourses; // استخدام العلاقة enrolledCourses()

        return response()->json($enrolledCourses);
    }
}
