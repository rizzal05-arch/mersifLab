<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\ClassModel;
use App\Models\Module;
use App\Models\User;

/**
 * ContentPolicy - Authorization untuk Class, Chapter, Module
 * 
 * Kontrol siapa yang bisa create, read, update, delete content
 */
class ContentPolicy
{
    /**
     * ==================== CLASS POLICIES ====================
     */

    /**
     * Semua orang (authenticated) bisa lihat list classes
     */
    public function viewAny(User $user)
    {
        return $user !== null;
    }

    /**
     * Siapa saja bisa lihat class yang published
     * Teacher pemilik & admin selalu bisa lihat
     */
    public function viewClass(User $user, ClassModel $class)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher() && $class->teacher_id === $user->id) {
            return true;
        }

        return $class->is_published;
    }

    /**
     * Hanya teacher yang bisa create class
     */
    public function createClass(User $user)
    {
        return $user->isTeacher() || $user->isAdmin();
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa update class
     */
    public function updateClass(User $user, ClassModel $class)
    {
        return $user->isAdmin() || ($user->isTeacher() && $class->teacher_id === $user->id);
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa delete class
     */
    public function deleteClass(User $user, ClassModel $class)
    {
        return $user->isAdmin() || ($user->isTeacher() && $class->teacher_id === $user->id);
    }

    /**
     * ==================== CHAPTER POLICIES ====================
     */

    /**
     * Siapa saja bisa lihat chapters dari class yang visible untuk mereka
     */
    public function viewChapter(User $user, Chapter $chapter)
    {
        return $this->viewClass($user, $chapter->class);
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa create chapter
     */
    public function createChapter(User $user, ClassModel $class)
    {
        return $this->updateClass($user, $class);
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa update chapter
     */
    public function updateChapter(User $user, Chapter $chapter)
    {
        return $this->updateClass($user, $chapter->class);
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa delete chapter
     */
    public function deleteChapter(User $user, Chapter $chapter)
    {
        return $this->updateClass($user, $chapter->class);
    }

    /**
     * ==================== MODULE POLICIES ====================
     */

    /**
     * Student & teacher bisa lihat module yang published
     * Teacher pemilik & admin selalu bisa lihat
     */
    public function viewModule(User $user, Module $module)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher() && $module->chapter->class->teacher_id === $user->id) {
            return true;
        }

        if ($user->isStudent() && $module->is_published) {
            return true;
        }

        return false;
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa create module
     */
    public function createModule(User $user, Chapter $chapter)
    {
        return $this->updateChapter($user, $chapter);
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa update module
     */
    public function updateModule(User $user, Module $module)
    {
        return $this->updateChapter($user, $module->chapter);
    }

    /**
     * Hanya teacher pemilik atau admin yang bisa delete module
     */
    public function deleteModule(User $user, Module $module)
    {
        return $this->updateChapter($user, $module->chapter);
    }

    /**
     * ==================== MANAGE CONTENT ====================
     */

    /**
     * Hanya teacher yang bisa manage content (di Profile section)
     */
    public function manageContent(User $user)
    {
        return $user->isTeacher() || $user->isAdmin();
    }
}
