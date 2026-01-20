<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\Response;

/**
 * CoursePolicy
 * 
 * Menentukan apakah user bisa perform action terhadap Course
 * 
 * Usage dalam route/controller:
 * $this->authorize('view', $course);
 * $this->authorize('create', Course::class);
 * $this->authorize('update', $course);
 */
class CoursePolicy
{
    /**
     * Determine whether the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        // Semua user authenticated bisa view courses
        return true;
    }

    /**
     * Determine whether the user can view the course.
     */
    public function view(User $user, Course $course): bool
    {
        // Student bisa view course jika sudah enroll
        // Teacher bisa view course yang dia buat
        // Admin bisa view semua course
        
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $course->teacher_id === $user->id;
        }

        if ($user->isStudent()) {
            // Check apakah student sudah enroll
            // return $user->enrolledCourses()->contains($course);
            return true; // Temporary allow all
        }

        return false;
    }

    /**
     * Determine whether the user can create courses.
     * Hanya TEACHER yang bisa create course
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can update the course.
     * Hanya TEACHER pemilik course yang bisa update
     */
    public function update(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher() && $course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course.
     * Hanya TEACHER pemilik atau ADMIN yang bisa delete
     */
    public function delete(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher() && $course->teacher_id === $user->id;
    }

    /**
     * Hanya teacher yang bisa manage materi
     */
    public function manageMaterial(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher() && $course->teacher_id === $user->id;
    }
}
