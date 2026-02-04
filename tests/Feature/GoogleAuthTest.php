<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Two\InvalidStateException;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_invalid_state_exception_falls_back_to_stateless_and_logs_in()
    {
        // Prepare a fake Socialite user (properties accessed directly in controller)
        $socialUser = (object) [
            'id' => 'google-id-123',
            'name' => 'Social Tester',
            'email' => 'social@example.com',
            'avatar' => null,
        ];

        // Mock the provider: first ->user() throws InvalidStateException, then stateless()->user() returns the user
        $provider = Mockery::mock();
        $provider->shouldReceive('user')->andThrow(new InvalidStateException())->once();
        $provider->shouldReceive('stateless')->andReturnSelf()->once();
        $provider->shouldReceive('user')->andReturn($socialUser)->once();

        \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        // Hit the callback route (Google would normally redirect here with code/state)
        $response = $this->get(route('auth.google.callback', ['code' => 'x', 'state' => 'y']));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'social@example.com']);
    }

    public function test_generic_exception_returns_friendly_error_message()
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('user')->andThrow(new \Exception('provider down'))->once();

        \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('auth.google.callback', ['code' => 'x', 'state' => 'y']));

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Google login failed', session('error'));
        $this->assertGuest();
    }
}
