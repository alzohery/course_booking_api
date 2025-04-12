<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:



# Course Booking API – Mini Version

## Overview

This project is a simple RESTful API for a course booking platform. It provides interfaces (APIs) for Instructors to add and manage courses, and for Students to view and book them. This API is built using Laravel.

## Key Features

* **User Authentication:** User registration and login with role specification (student/instructor) using Laravel Sanctum.
* **Instructor Course Management:**
    * Create new courses.
    * Modify details of existing courses (only their own).
    * Delete courses (only their own).
    * View a list of courses created by them only.
* **Student Course Access:**
    * View a list of all available courses.
    * Book courses (enrollment).
    * View a list of courses they have booked.
* **Business Rules:**
    * Instructors cannot book courses.
    * Students cannot modify or delete courses.
    * Students cannot book the same course twice.
    * Courses cannot accept bookings after reaching the `max_students` limit.

## Technologies Used

* Laravel (Latest Stable Version)
* PHP
* MySQL (or any Laravel-supported database)
* Laravel Sanctum (for Authentication)

## Prerequisites

* PHP >= 8.1
* Composer
* Node.js and npm (if you plan to run any frontend assets - not strictly required for this API)
* Web Server (e.g., Apache or Nginx)
* MySQL Database (or your configured database)

## Setup Instructions

1.  **Clone the Repository:**
    ```bash
    git clone [Your GitHub Repository Link Here]
    cd course_booking_api
    ```

2.  **Install Composer Dependencies:**
    ```bash
    composer install
    ```

3.  **Copy the Environment File (.env):**
    ```bash
    cp .env.example .env
    ```

4.  **Configure the Environment File (.env):**
    * Modify the database variables (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, etc.) to match your database setup.
    * Configure `APP_URL` to match your project's URL (usually `http://localhost:8000` for local development).

5.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

6.  **Run Migrations and Seeders:**
    ```bash
    php artisan migrate --seed
    ```
    * `--seed` will run any seeders you have created to populate the database with initial data (if applicable).

7.  **Install Laravel Sanctum:**
    ```bash
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    php artisan migrate
    ```

8.  **Start the Laravel Development Server:**
    ```bash
    php artisan serve
    ```
    * The API will typically be accessible at `http://127.0.0.1:8000`. **Note:** This is a development server; use a proper web server (like Apache or Nginx) for production.

## API Endpoints

| Method    | Path                      | Description                                                                  | Authentication Required | Role(s) Required   | HTTP Status |
| :-------- | :------------------------ | :--------------------------------------------------------------------------- | :---------------------- | :----------------- | :---------- |
| POST      | `/api/register`            | Register a new user (specifies `student` or `instructor` role)               | No                        | None               | 201 Created   |
| POST      | `/api/login`               | Log in an existing user and receive an Access Token                           | No                        | None               | 200 OK      |
| POST      | `/api/logout`              | Log out the authenticated user (requires a valid Token)                      | Yes                       | Authenticated      | 200 OK      |
| GET       | `/api/user`                | View information about the authenticated user                               | Yes                       | Authenticated      | 200 OK      |
| GET       | `/api/courses`             | View a list of all courses                                                   | Yes                       | Authenticated      | 200 OK      |
| POST      | `/api/courses`             | Create a new course (for instructors)                                        | Yes                       | instructor         | 201 Created   |
| GET       | `/api/courses/{course}`    | View details of a specific course                                            | Yes                       | Authenticated      | 200 OK      |
| PUT/PATCH | `/api/courses/{course}`    | Update a specific course (for the instructor who created it)                    | Yes                       | instructor (owner) | 200 OK      |
| DELETE    | `/api/courses/{course}`    | Delete a specific course (for the instructor who created it)                    | Yes                       | instructor (owner) | 204 No Content |
| GET       | `/api/instructor/courses`  | View courses created by the authenticated instructor only                     | Yes                       | instructor         | 200 OK      |
| POST      | `/api/enrollments`         | Enroll in a course (for students)                                            | Yes                       | student            | 201 Created   |
| GET       | `/api/student/enrollments`  | View courses booked by the authenticated student                               | Yes                       | student            | 200 OK      |

## Postman Collection

[Course Booking API.postman_collection.json]

## Testing Instructions with Postman

1.  **Import the Postman Collection (if provided).**
2.  **Register a New User (POST `/api/register`):** In the request body (raw JSON), include `name`, `email`, `password`, `password_confirmation`, and `role` (either `"student"` or `"instructor"`).
3.  **Log In to Get Access Token (POST `/api/login`):** In the request body (raw JSON), include the `email` and `password` of the registered user. The response will contain an `access_token`.
4.  **Use the Access Token:** For any authenticated requests, go to the "Authorization" tab in Postman, select "Bearer Token", and paste the `access_token` you received during login. You may want to save this token as a Postman environment variable for easier use.
5.  **Test the Endpoints:**
    * **As an Instructor:**
        * Create a new course (POST `/api/courses`) with `title`, `description`, and `max_students` in the request body (raw JSON).
        * View your own courses (GET `/api/instructor/courses`).
        * Update your course (PUT/PATCH `/api/courses/{course_id}`) with the course ID and updated details in the request body (raw JSON).
            * `PUT` is typically used to replace the entire resource.
            * `PATCH` is typically used to update specific fields.
        * Delete your course (DELETE `/api/courses/{course_id}`) with the course ID.
        * Attempt to update or delete a course created by another instructor (should result in a 403 Forbidden response).
        * Attempt to book a course (POST `/api/enrollments`) (should result in a 403 Forbidden response).
    * **As a Student:**
        * View all courses (GET `/api/courses`).
        * Book a course (POST `/api/enrollments`) with the `course_id` in the request body (raw JSON).
        * View your booked courses (GET `/api/student/enrollments`).
        * Attempt to book the same course twice (should result in a 409 Conflict response).
        * Attempt to book a course that has reached its `max_students` limit (should result in a 400 Bad Request response).
        * Attempt to create, update, or delete courses (should result in a 401 Unauthorized or 403 Forbidden response).

## Error Handling

* The API returns standard HTTP status codes to indicate success or failure.
* Error responses are typically in JSON format and include a `message` field describing the error.
* Validation errors are returned with a 422 Unprocessable Entity status code and an `errors` object containing details about each validation failure.

## Additional Notes

* This API uses Laravel Sanctum for simple token-based authentication.
* Course ownership for modification and deletion is determined by the `instructor_id` associated with the course.
* Enrollment records are stored in the `enrollments` table, linking students to courses.

## Author

[Mohamed Alzohery]
[mohamedmohasenalzohery@gmail.com]