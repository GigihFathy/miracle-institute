<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('id');
    }

    public function test_registration_creates_an_unverified_user_and_sends_verification_email(): void
    {
        Notification::fake();
        Role::create([
            'name' => 'student',
            'label' => 'Student',
        ]);

        Livewire::test(Register::class)
            ->set('name', 'Budi')
            ->set('email', 'budi@example.test')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('submit')
            ->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'budi@example.test')->first();

        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->roles()->where('name', 'student')->exists());

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_cannot_login_before_verifying_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'andi@example.test',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        Livewire::test(Login::class)
            ->set('email', 'andi@example.test')
            ->set('password', 'password123')
            ->set('remember', true)
            ->call('submit')
            ->assertRedirect(route('login'));

        $this->assertGuest();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_is_blocked_from_verified_routes(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_signed_verification_link_marks_email_as_verified(): void
    {
        Role::create([
            'name' => 'student',
            'label' => 'Student',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('redirect.by.role'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
