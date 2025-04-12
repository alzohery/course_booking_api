<?php
/*
|--------------------------------------------------------------------------
| CourseController.php Actions
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This controller manages course-related operations for the Course Booking API.
| It includes methods for creating, retrieving, updating, and deleting courses,
| with role-based authorization to ensure only instructors can manage their own courses
| and all authenticated users can view course details.
|
| 1. index():
|    Retrieves and returns a listing of all available courses.
|    This method is accessible to any authenticated user (student or instructor).
|    Returns a JSON response containing an array of course objects.
|
| 2. store(Request $request):
|    Handles the creation of a new course.
|    Accessible only to authenticated users with the 'instructor' role.
|    Validates the incoming request data (title, description, max_students).
|    Creates a new course record in the database, associating it with the
|    ID of the currently authenticated instructor.
|    Returns a JSON response containing the newly created course object with a 201 Created status.
|
| 3. show(Course $course):
|    Retrieves and returns the details of a specific course.
|    Accessible to any authenticated user (student or instructor).
|    Receives the course object via route model binding.
|    Returns a JSON response containing the details of the requested course.
|
| 4. update(Request $request, Course $course):
|    Handles the updating of an existing course's details.
|    Accessible only to authenticated users with the 'instructor' role who own the course
|    (checked via authorization policies).
|    Validates the incoming request data (title, description, max_students).
|    Updates the specified course record in the database.
|    Returns a JSON response containing the updated course object.
|    Returns a 403 Forbidden status if the authenticated instructor does not own the course.
|
| 5. destroy(Course $course):
|    Handles the deletion of an existing course.
|    Accessible only to authenticated users with the 'instructor' role who own the course
|    (checked via authorization policies).
|    Deletes the specified course record from the database.
|    Returns a 204 No Content status upon successful deletion.
|    Returns a 403 Forbidden status if the authenticated instructor does not own the course.
|
*/
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller; 

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
        return response()->json($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     \Log::info('Request received for store:', $request->all());
    //     try {
    //         $request->validate([
    //             'instructor_id' => 'required|exists:users,id,role,instructor',
    //             'title' => 'required|string|max:255',
    //             'description' => 'required|string',
    //             'max_students' => 'required|integer|min:1',
    //         ]);

    //         $course = Course::create($request->all());
    //         \Log::info('Course created:', $course->toArray());
    //         return response()->json($course, 201);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         \Log::error('Validation error:', $e->errors());
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         \Log::error('An unexpected error occurred:', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    //         return response()->json(['error' => 'Internal Server Error'], 500);
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'max_students' => 'required|integer|min:1',
        ]);

        $course = $request->user()->courses()->create([
            'title' => $request->title,
            'description' => $request->description,
            'max_students' => $request->max_students,
        ]);

        return response()->json($course, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return response()->json($course);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Course $course)
    // {
    //     $request->validate([
    //         'title' => 'sometimes|string|max:255', // 'sometimes' يعني أنه مطلوب إذا تم تقديمه
    //         'description' => 'sometimes|string',
    //         'max_students' => 'sometimes|integer|min:1',
    //         // لا نسمح بتحديث instructor_id عبر هذا الـ endpoint
    //     ]);

    //     $course->update($request->all());
    //     return response()->json($course);
    // }

    public function update(Request $request, Course $course)
    {
        if ($request->user()->id !== $course->instructor_id) {
            return response()->json(['message' => 'Unauthorized to update this course'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'max_students' => 'sometimes|integer|min:1',
        ]);

        $course->update($request->all());
        return response()->json($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Course $course)
    // {
    //     $course->delete();
    //     return response()->noContent(); 
    // }

    

    public function destroy(Request $request, Course $course)
    {
        if ($request->user()->id !== $course->instructor_id) {
            return response()->json(['message' => 'Unauthorized to delete this course'], 403);
        }

        $course->delete();
        return response()->noContent();
    }



    public function instructorCourses(Request $request)
    {
        $instructor = $request->user();
        $courses = $instructor->courses; // استخدام العلاقة courses() لجلب كورسات المدرس

        return response()->json($courses);
    }
}