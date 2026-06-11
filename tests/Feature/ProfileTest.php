<?php
 
namespace Tests\Feature;
 
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
 
class ProfileTest extends TestCase
{
    use RefreshDatabase;
 
    public function test_settings_page_is_displayed(): void
    {
        $user = User::factory()->create();
 
        $response = $this
            ->actingAs($user)
            ->get('/settings');
 
        $response->assertOk();
        $response->assertSee('Pengaturan Akun');
    }
 
    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();
 
        $response = $this
            ->actingAs($user)
            ->delete('/settings/delete-account');
 
        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }
}
