<?php
/*
|--------------------------------------------------------------------------
| Kernel.php
|--------------------------------------------------------------------------
| Made by Mohamed Alzohery
|--------------------------------------------------------------------------
| This file defines the HTTP kernel for the Laravel application. The HTTP
| kernel is responsible for bootstrapping the framework and processing
| incoming HTTP requests. It defines the global middleware stack, route
| middleware groups, and individual route middleware that are applied
| during the request lifecycle.
|
| protected $middleware:
|   This array defines the application's global HTTP middleware stack.
|   These middleware are executed for every request that enters the application.
|   They perform tasks such as trusting proxies, preventing maintenance mode,
|   validating post size, trimming strings, converting empty strings to null,
|   and handling Cross-Origin Resource Sharing (CORS) if enabled.
|   The order in this array is significant as middleware are executed in the
|   order they are listed.
|
| protected $middlewareGroups:
|   This array defines named groups of middleware that can be easily applied
|   to routes. Laravel provides two default groups:
|   - 'web': Contains middleware that are common for web routes, such as cookie
|     encryption and handling, session management, CSRF protection, and route
|     model binding.
|   - 'api': Contains middleware that are typically used for API routes, such as
|     rate limiting ('throttle:api') and route model binding.
|   We have ensured that the 'api' middleware group does NOT include the standard
|   'auth' middleware, as API authentication is handled differently (using Sanctum).
|
| protected $routeMiddleware:
|   This array defines individual middleware with assigned keys (aliases). These
|   middleware can be applied to specific routes or middleware groups by their
|   defined keys.
|   - 'auth': Maps to the `\App\Http\Middleware\Authenticate::class` middleware
|     that we (potentially) created or modified to handle API authentication
|     redirection correctly.
|   - 'auth.basic': Middleware for HTTP basic authentication.
|   - 'cache.headers': Middleware for setting cache headers.
|   - 'can': Middleware for authorizing user actions based on defined abilities.
|   - 'guest': Middleware for redirecting authenticated users from guest-only routes.
|   - 'signed': Middleware for validating signed route URLs.
|   - 'throttle': Middleware for rate limiting requests.
|   - 'verified': Middleware for ensuring users have verified their email addresses.
|   - 'auth:sanctum': Middleware provided by Laravel Sanctum for authenticating
|     API requests using tokens. Our API routes are protected using this middleware.
|
| protected $middlewarePriority:
|   This array defines the priority order in which middleware are run. This allows
|   certain middleware to always run before or after others, regardless of their
|   order in the `$middleware` or `$middlewareGroups` arrays. This can be important
|   for middleware that depend on the output of other middleware (e.g., session
|   middleware should typically run before middleware that use session data).
|   We have ensured that our custom `\App\Http\Middleware\Authenticate::class` is
|   positioned appropriately in this priority list.
|
| In summary, the `Kernel.php` file is crucial for configuring the HTTP request
| lifecycle in our Laravel API. We have specifically configured the 'api' middleware
| group to exclude traditional web authentication and ensured that the 'auth:sanctum'
| middleware is used for protecting our API routes. We also addressed the
| "Route [login] not defined" error by potentially creating or modifying the 'auth'
| middleware to handle API authentication failures with a 401 response instead of
| redirecting to a web 'login' route.
*/
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


    public function enrollments(): BelongsToMany 
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')->withTimestamps();
    }
}