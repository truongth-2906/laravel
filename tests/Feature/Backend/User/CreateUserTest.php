<?php

namespace Tests\Feature\Backend\User;

use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CreateUserTest.
 */
class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_access_the_create_user_page()
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin/auth/user/create');

        $response->assertOk();
    }

    /** @test */
    public function create_user_requires_validation()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/auth/user');

        $response->assertSessionHasErrors(['type', 'name', 'email', 'password']);
    }

    /** @test */
    public function user_email_needs_to_be_unique()
    {
        $this->loginAsAdmin();

        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post('/admin/auth/user', [
            'email' => 'john@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function only_admin_can_create_users()
    {
        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get('/admin/auth/user/create');

        $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
    }
}
