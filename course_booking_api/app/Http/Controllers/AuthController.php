<?php
/*
|--------------------------------------------------------------------------
| AuthController.php Actions
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This controller handles user authentication for the Course Booking API.
| It includes methods for user registration, login, and logout, leveraging
| Laravel Sanctum for API token management.
|
| 1. register(Request $request): Handles the registration of new users,
|    validates input, creates a new user, generates a Sanctum token, and
|    returns the user and token in a JSON response.
|
| 2. login(Request $request): Handles the login of existing users,
|    authenticates the user based on email and password, generates a
|    Sanctum token upon successful authentication, and returns the user
|    and token in a JSON response. Returns an error for invalid credentials.
|
| 3. logout(Request $request): Handles the logout of authenticated users,
|    revokes the current Sanctum access token associated with the user,
|    and returns a JSON response indicating successful logout.
|
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,instructor',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } else {
            return response()->json(['message' => 'Not authenticated'], 401); // أو أي استجابة مناسبة
        }
    }

}
