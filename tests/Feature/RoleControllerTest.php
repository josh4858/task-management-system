<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\TaskSeeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Task;


class RoleControllerTest extends TestCase
{

    use RefreshDatabase;

    const ADMIN = "admin";
    const USER = "user";
    protected $user;

    public function prepDatabaseAndAuthActingUser($role_name) {
        // This function will prepare the database and get the acting user ready
        $this->seed(RoleSeeder::class);
        // Seed the user table with some temp data
        $this->seed(UserSeeder::class);
        // Seed the task table with some temp data
        $this->seed(TaskSeeder::class);

        // Simulate a test as an ADMIN user, this won't work for a standard user
        $role = Role::where('name', $role_name)->first();
        $role_id = $role->id;
        $this->user = User::factory()->create(['role_id' => $role_id]);

    
        // Authenticate the user and obtain the auth token
        $token = auth()->login($this->user);
        // Store the token in the request headers for subsequent requests 
        $this->withHeaders(['Authorization' => 'Bearer ' . $token]);
        // Return $this to ensure method chaining works correctly
        return $this;
    }

    /** @test */
    public function can_admin_view_all_roles() {
        // Setup
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);
        // Action
        $response = $this->actingAs($this->user)->getJson('/api/roles');
        // Assertion
        $response->assertStatus(200);

    }
    /** @test */
    public function can_admin_create_role() {
        // Setup
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        // Create test role
        $role = [
            'name' => "TestRole"
        ];
        // Action
        $response = $this->actingAs($this->user)->postJson('/api/roles', $role);

        // Assertion
        $response->assertStatus(201);
        
    }
    /** @test */
    public function can_admin_update_role() {
        // Setup
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        $oldRoleData = [
            'name' => "OldRole"
        ];
        // Create that role in the db
        $oldRole = Role::create($oldRoleData);

        $updatedRole = [
            'name' => "NewRole"
        ];
        // Action
        $response = $this->actingAs($this->user)->putJson('/api/roles/'.$oldRole->id,$updatedRole);

        // Assertion
        $response->assertStatus(200);

    }
    /** @test */
    public function can_admin_delete_role() {
        // Setup
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);
        $oldRoleData = [
            'name' => "OldRole"
        ];
        // Create that role in the db
        $role = Role::create($oldRoleData);

        // Action11
        $response = $this->actingAs($this->user)->deleteJson('/api/roles/'.$role->id);

        // Assertion
        $response->assertStatus(204);

    }
}
