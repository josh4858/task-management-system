<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;


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
            'role_id' => 2, // Assuming 2 is the role ID for 'User'
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
}
