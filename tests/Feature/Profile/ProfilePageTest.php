<?php

namespace Tests\Feature\Profile;

use App\Livewire\Profile\ProfilePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_information_rejects_empty_required_fields(): void
    {
        $user = User::factory()->create([
            'gender' => 'male',
            'dob' => '2000-01-01',
        ]);

        $this->actingAs($user);

        Livewire::test(ProfilePage::class)
            ->set('name', '')
            ->set('email', '')
            ->set('phone', '')
            ->set('gender', '')
            ->set('dob', '')
            ->call('save')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'gender' => 'required',
                'dob' => 'required',
            ]);
    }

    public function test_password_update_rejects_invalid_values(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
            'gender' => 'male',
        ]);

        $this->actingAs($user);

        Livewire::test(ProfilePage::class)
            ->set('currentPassword', 'wrong-password')
            ->set('password', 'short')
            ->set('password_confirmation', 'different')
            ->call('updatePassword')
            ->assertHasErrors(['currentPassword', 'password']);

        $this->assertTrue(Hash::check('Password123!', $user->fresh()->password));
    }

    public function test_password_update_rejects_empty_fields(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
            'gender' => 'male',
        ]);

        $this->actingAs($user);

        Livewire::test(ProfilePage::class)
            ->call('updatePassword')
            ->assertSet('activeTab', 'password')
            ->assertHasErrors([
                'currentPassword' => 'required',
                'password' => 'required',
                'password_confirmation' => 'required',
            ]);
    }

    public function test_account_information_rejects_phone_without_62_prefix(): void
    {
        $user = User::factory()->create([
            'gender' => 'male',
            'dob' => '2000-01-01',
        ]);

        $this->actingAs($user);

        Livewire::test(ProfilePage::class)
            ->set('phone', '08123456789')
            ->call('save')
            ->assertHasErrors(['phone' => 'regex']);
    }

    public function test_password_update_saves_a_valid_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
            'gender' => 'male',
        ]);

        $this->actingAs($user);

        Livewire::test(ProfilePage::class)
            ->set('currentPassword', 'Password123!')
            ->set('password', 'NewPassword456')
            ->set('password_confirmation', 'NewPassword456')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertRedirect(localized_route('login'));

        $this->assertTrue(Hash::check('NewPassword456', $user->fresh()->password));
        $this->assertGuest();
    }
}
