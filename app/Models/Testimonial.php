<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'content',
        'avatar',
        'is_published',
        'admin_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function avatarUrl()
    {
        // Prefer admin's profile avatar if available
        if ($this->admin && isset($this->admin->avatar) && $this->admin->avatar) {
            return Storage::disk('public')->url($this->admin->avatar);
        }

        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        }

        $name = urlencode($this->name ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=667eea&color=fff";
    }
}
