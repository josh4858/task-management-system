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

class TaskControllerTest extends TestCase
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
    public function can_user_get_all_their_tasks() {
        // Prep
        $this->prepDatabaseAndAuthActingUser(self::USER);
        // Action
        $response = $this->actingAs($this->user)->getJson('/api/user_tasks');
        // Assertion
        $response->assertStatus(200);
    }

    // Can Admin get all tasks
    /** @test */
    public function can_admin_fetch_all_tasks() {
        // Prep
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);
        // Action 
        $response = $this->actingAs($this->user)->getJson('/api/tasks');
        // Assert
        $response->assertStatus(200);
    }

    // Can Admin delete a users task for them
    /** @test */
    public function can_admin_get_single_task_for_specific_user() {
        // Prep
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        // Get a random test user
        // Get random seeded admin user 
        $randUser = User::whereHas('role', function($query) {
            $query->where('name','admin');
        })->inRandomOrder()->first();

        // Action
        $response = $this->actingAs($this->user)->getJson('/api/tasks/'.$randUser->id);

        // Assertion
        $response->assertStatus(200);

    }
        // Can Admin create a single task for a specific user

    /** @test */
    public function can_admin_create_single_task_for_specific_user() {
        // Prep
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        $newTaskData = [
            'title' => "This is a New task",
            'description' => "This is a test task description",
            'completed' => 0,
        ];

        // Get random user 
        $randUser = User::whereHas('role', function($query) {
            $query->where('name', 'user');
        })->inRandomOrder()->first();


        // Action
        $response = $this->actingAs($this->user)->postJson('/api/users/'. $randUser->id. '/tasks/', $newTaskData);

        // Assert
        $response->assertStatus(201);

 }

    // Can Admin update a single task for a specific user
    /** @test */
    public function can_admin_update_single_task_for_specific_user() {
        // Prep
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);
        
        // Get random user 
        $randUser = User::whereHas('role', function($query) {
            $query->where('name', 'user');
        })->inRandomOrder()->first();

        // Create a task for the user
        $task = $randUser->tasks()->create([
            'title' => "Old task name",
            'description' => "Old task description",
            'completed' => 0,
        ]);

        // Updated task data
        $updatedTaskData = [
            'title' => "This is a updated task title",
            'description' => "This is a updated task description",
            'completed' => 0,
        ];

        // Action
        $response = $this->actingAs($this->user)->putJson('/api/users/'. $randUser->id .'/tasks/'. $task->id, $updatedTaskData);

        // Assert
        $response->assertStatus(201); // Check if update was successful

    }



}
