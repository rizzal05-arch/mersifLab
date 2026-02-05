<?php

namespace Tests\Feature;

use App\Models\TeacherApplication;
use App\Models\User;
use Tests\TestCase;

class TeacherApplicationCRUDTest extends TestCase
{
    protected $user;
    protected $application;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create(['role' => 'student']);
        
        // Create a test application
        $this->application = TeacherApplication::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);
    }

    /**
     * Test that user can view their application preview
     */
    public function test_user_can_preview_own_application()
    {
        $this->actingAs($this->user)
            ->get(route('teacher.application.preview'))
            ->assertStatus(200)
            ->assertViewIs('teacher.application-preview')
            ->assertViewHas('application');
    }

    /**
     * Test that user cannot view another user's application
     */
    public function test_user_cannot_preview_other_application()
    {
        $otherUser = User::factory()->create(['role' => 'student']);
        
        $this->actingAs($otherUser)
            ->get(route('teacher.application.preview'))
            ->assertStatus(403);
    }

    /**
     * Test that user can view edit form for pending application
     */
    public function test_user_can_edit_pending_application()
    {
        $this->actingAs($this->user)
            ->get(route('teacher.application.edit'))
            ->assertStatus(200)
            ->assertViewIs('teacher.application-edit')
            ->assertViewHas('application');
    }

    /**
     * Test that user cannot edit approved application
     */
    public function test_user_cannot_edit_approved_application()
    {
        $this->application->update(['status' => 'approved']);
        
        $this->actingAs($this->user)
            ->get(route('teacher.application.edit'))
            ->assertRedirect(route('teacher.application.preview'))
            ->assertSessionHas('info');
    }

    /**
     * Test that user can update pending application
     */
    public function test_user_can_update_pending_application()
    {
        $newData = [
            'full_name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '08123456789',
            'address' => 'New Address',
            'teaching_experience' => 'Updated experience',
            'portfolio_link' => 'https://example.com'
        ];

        $this->actingAs($this->user)
            ->put(route('teacher.application.update'), $newData)
            ->assertRedirect(route('teacher.application.preview'))
            ->assertSessionHas('success');

        $this->application->refresh();
        $this->assertEquals($newData['full_name'], $this->application->full_name);
        $this->assertEquals($newData['email'], $this->application->email);
        $this->assertEquals('pending', $this->application->status);
    }

    /**
     * Test that update resets status to pending
     */
    public function test_update_resets_status_to_pending()
    {
        $this->application->update(['status' => 'rejected']);
        
        $newData = [
            'full_name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '08123456789',
            'address' => 'New Address',
            'teaching_experience' => 'Updated experience',
        ];

        $this->actingAs($this->user)
            ->put(route('teacher.application.update'), $newData);

        $this->application->refresh();
        $this->assertEquals('pending', $this->application->status);
    }

    /**
     * Test validation on update
     */
    public function test_update_validates_required_fields()
    {
        $this->actingAs($this->user)
            ->put(route('teacher.application.update'), [
                'full_name' => '',
                'email' => 'invalid-email',
            ])
            ->assertSessionHasErrors(['full_name', 'email']);
    }
}
