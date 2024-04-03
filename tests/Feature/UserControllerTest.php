<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use App\Models\User;
use App\Models\Role;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    // Define the constant names

    const ADMIN = "admin";
    const USER = "user";
    protected $user;

    public function prepDatabaseAndAuthActingUser($role_name) {
        // This function will prepare the database and get the acting user ready
        $this->seed(RoleSeeder::class);
        // Seed the user table with some temp data
        $this->seed(UserSeeder::class);
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
    public function user_can_register_successfully()
    {
        // Seed the roles table before the test
        $this->seed(RoleSeeder::class);
        // Arrange: Prepare the data for a new user
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password', // Assume your validation rules accept this format
            'password_confirmation' => 'password', // Needed for registration validation
            'role_id' => Role::inRandomOrder()->first()->id, // Assuming 2 is the role ID for 'User'
        ];
        // Act: Send a POST request to the register endpoint and receive a response
        $response = $this->postJson('/api/register', $userData);

        // Assert: Check the response status and structure
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'User registered successfully',
                 ])
                 ->assertJsonStructure([
                     'message', 'token'
                 ]);
                 
        // Additional Assertions: Check the database has the new user
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
        /** @test */
        public function can_admin_user_log_in_successfully() {
            // Add a test user to the database
            $this->prepDatabaseAndAuthActingUser(self::ADMIN);
            // Attempt to login via the endpoint
            $response = $this->postJson('/api/login',[
                'email' => $this->user->email,
                'password' => 'password',
            ]);
            // Capture response and assert Json returns success code and token
            $response->assertStatus(200);
        }

            /** @test */
    public function can_admin_get_all_users_successfully() {

        // User the prep function to prepare the user for this function
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        // Check the count of users before the test
        $userCountBefore = User::count();

        // Act:: Send GET request to get user data
  
        $response = $this->actingAs($this->user)->getJson('/api/users'); 
        
        // Assert: Check the response status and structure
        $response->assertStatus(201);

        // Check the count of users after the test
        $userCountAfter = User::count();

        // Assert that the count of users remains the same
        $this->assertEquals($userCountBefore, $userCountAfter);
    }

    /** @test */

    public function can_admin_get_a_single_user() {
         // Prep the acting user for test as ADMIN
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        // Get random seeded admin user 
        $randAdmin = User::whereHas('role', function($query) {
            $query->where('name','admin');
        })->inRandomOrder()->first();

        // ACT: Send GET request to get a single user
        $response = $this->actingAs($this->user)->getJson('/api/users/'.$randAdmin->id); 

        // Assert: Check response status
        $response->assertStatus(200);
        
    }

    /** @test */
    public function can_admin_delete_a_test_user() {
        // Prep the acting user for test as ADMIN
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        // Create new test user to delete
        $newTestUser = [
            'name' => 'New Test User',
            'email' => 'newtestuser@example.com',
            'password' => 'password', // Assume your validation rules accept this format
            'password_confirmation' => 'password', // Needed for registration validation
            'role_id' => Role::inRandomOrder()->first()->id, // Assuming 2 is the role ID for 'User'
        ];

        // Create that user
        $newUser = User::create($newTestUser);

        // ACT:: send delete request to delete a single user
        $response = $this->actingAs($this->user)->deleteJson('/api/users/' . $newUser->id);
        
        // Assert response is 204 meaning sucessfully deleted a user
        $response->assertStatus(204);
    }

    /** @test */
    public function can_admin_update_test_user() {
        // Prep the acting user for test as ADMIN
        $this->prepDatabaseAndAuthActingUser(self::ADMIN);

        // Get a random user with user role 
        $randUser = User::whereHas('role', function($query) {
            $query->where('name','user');
        })->inRandomOrder()->first();

        // ACT:: send request to update a single user with payload
        $response = $this->actingAs($this->user)->putJson('/api/users/'.$randUser->id, [
            'name' => 'New Updated Name',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function can_user_get_their_user_details() {
        // Setup acting user
        $this->prepDatabaseAndAuthActingUser(self::USER);
        // Action
        $response = $this->actingAs($this->user)->getJson('/api/user_details');
        // Assertion
        $response->assertStatus(200);
    }


}
