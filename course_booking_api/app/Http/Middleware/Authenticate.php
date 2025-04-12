<?php
/*
|--------------------------------------------------------------------------
| Authenticate.php Middleware
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This middleware is responsible for ensuring that the user making a request
| to the application is authenticated. It extends Laravel's base Authenticate
| middleware and customizes the redirection behavior for API requests.
|
| handle(Request $request, Closure $next, ...$guards):
|   This is the main method that gets executed when a request passes through
|   this middleware.
|   - It calls the `authenticate()` method to verify if the user is authenticated
|     using the specified authentication guards (in our case, likely 'sanctum' for API).
|   - If the user is not authenticated, it calls the `unauthenticated()` method
|     to handle the unauthenticated request.
|   - If the user is authenticated, it allows the request to proceed further
|     in the middleware pipeline by calling `$next($request)`.
|
| authenticate(Request $request, array $guards):
|   This method attempts to authenticate the user using the provided authentication
|   guards.
|   - It iterates through the specified guards.
|   - For each guard, it tries to authenticate the user using the `auth()->guard($guard)->user()` method.
|   - If a user is successfully authenticated with any of the guards, the authenticated
|     user object is stored using `Auth::setUser($user)`, and the method returns.
|   - If no guard successfully authenticates the user, it throws an `AuthenticationException`.
|
| redirectTo(Request $request): ?string
|   This method is called when the `authenticate()` method throws an
|   `AuthenticationException` and the request is not expecting a JSON response.
|   - It checks if the request's `expectsJson()` method returns false. This typically
|     indicates a traditional web request (not an API request expecting JSON).
|   - If it's a web request, it returns the URL of the login route ('login'),
|     redirecting the unauthenticated user to the login page.
|   - If the request expects a JSON response (likely an API request), it returns `null`,
|     indicating that no redirection should occur. In this scenario, the
|     `unauthenticated()` method will handle sending an appropriate JSON error response.
|
| unauthenticated($request, array $guards): void
|   This method is called when the user is not authenticated and the
|   `authenticate()` method throws an `AuthenticationException`.
|   - It checks if the request is expecting a JSON response using `$request->expectsJson()`.
|   - If it's expecting JSON, it sends a JSON response with a 401 Unauthorized
|     HTTP status code and an 'Unauthenticated.' message.
|   - If it's not expecting JSON, it redirects the user to the URL returned by
|     the `redirectTo()` method.
|
| In summary, this `Authenticate` middleware ensures that only authenticated users can
| access protected routes. It differentiates between web requests (redirecting to the
| login page) and API requests (returning a 401 Unauthorized JSON response) when
| a user is not authenticated. Our custom implementation likely adjusted the
| `redirectTo()` method to prevent redirection to a web 'login' route for API
| requests, which was causing the "Route [login] not defined" error during API logout.
*/
namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}