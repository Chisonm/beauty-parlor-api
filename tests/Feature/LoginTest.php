<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => Hash::make('secret123$')]);
    }

    /**
     * Test that User can login
     */
    public function testThatUserCanLogin()
    {
        $data = [
            'email' => $this->user->email,
            'password' => 'secret123$',
        ];

        //attempt login
        $response = $this->postJson('/api/v1/signin', $data);
        //Assert it was successful and a token was received
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }

    /**
     * Test that User can not login with wrong credentials
     */
    public function testLoginWithWrongCredentials()
    {
        //Create user
        $data = [
            'email' => 'test@gmail.com',
            'password' => 'secret123',
        ];

        //attempt login
        $response = $this->postJson('/api/v1/signin', $data);
        //Assert it was successful and a token was received
        $response->assertStatus(500);
        $this->assertArrayHasKey('error_debug', $response->json());
    }

    /**
     * Test that the fields are required
     */
    public function testLoginWithRequiredFields()
    {
        $data = [
            'email' => '',
            'password' => '',
        ];

        //attempt login
        $response = $this->postJson('/api/v1/signin', $data);

        //Assert email and password are required
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }

    /**
     * Test that the email is valid
     */
    public function testLoginWithInValidEmail()
    {
        $data = [
            'email' => 'test',
            'password' => 'secret123$',
        ];

        //attempt login
        $response = $this->postJson('/api/v1/signin', $data);
        //Assert wrong email format
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }
}
