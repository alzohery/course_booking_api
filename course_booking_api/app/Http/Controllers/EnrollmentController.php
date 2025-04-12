<?php

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

        // التحقق من أن المستخدم طالب
        if ($student->role !== 'student') {
            return response()->json(['message' => 'Only students can enroll in courses.'], 403);
        }

        // التحقق من أن الطالب لم يحجز الكورس من قبل
        if (Enrollment::where('student_id', $student->id)->where('course_id', $courseId)->exists()) {
            return response()->json(['message' => 'You are already enrolled in this course.'], 409); // 409 Conflict
        }

        // التحقق من أن الكورس لم يصل إلى الحد الأقصى لعدد الطلاب
        if ($course->enrollments()->count() >= $course->max_students) {
            return response()->json(['message' => 'This course is full.'], 400); // 400 Bad Request
        }

        // إنشاء سجل الحجز
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
