<?php

namespace Tests\Feature\Backend\User;

use App\Domains\Auth\Events\User\UserUpdated;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class UpdateUserTest.
 */
class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_access_the_edit_user_page()
    {
        $this->loginAsAdmin();

        $user = User::factory()->create();

        $response = $this->get('/admin/auth/user/'.$user->id.'/edit');

        $response->assertOk();
    }

    /** @test */
    public function only_the_master_admin_can_edit_themselves()
    {
        $admin = $this->loginAsAdmin();

        $this->get("/admin/auth/user/{$admin->id}/edit")->assertOk();

        $this->logout();

        $otherAdmin = User::factory()->admin()->create();
        $otherAdmin->assignRole(config('base.access.role.admin'));

        $this->actingAs($otherAdmin);

        $response = $this->get("/admin/auth/user/{$admin->id}/edit");

        $response->assertSessionHas('flash_danger', __('Only the administrator can update this user.'));
    }

    /** @test */
    public function the_master_admins_abilities_can_not_be_modified()
    {
        $admin = $this->loginAsAdmin();

        $role = Role::factory()->create();

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $admin->id,
        ]);

        $this->patch("/admin/auth/user/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'roles' => [$role->id],
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $admin->id,
        ]);
    }

    /** @test */
    public function only_admin_can_update_roles()
    {
        $this->actingAs(User::factory()->admin()->create());

        $user = User::factory()->admin()->create(['name' => 'John Doe']);

        $response = $this->patch("/admin/auth/user/{$user->id}", [
            'type' => User::TYPE_EMPLOYER,
            'name' => 'Jane Doe',
        ]);

        $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'type' => User::TYPE_ADMIN,
            'name' => 'John Doe',
        ]);
    }
}
