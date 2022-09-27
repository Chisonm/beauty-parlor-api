<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that User can register
     */
    public function testThatUserCanRegister()
    {
        //attempt register
        $response = $this->postJson('/api/v1/signup', $this->payload());

        //Assert it was successful and a token was received
        $response->assertStatus(201);
        $this->assertArrayHasKey('token', $response->json());
    }

    /**
     * Test that User can not register with wrong credentials
     */
    public function testRegisterWithWrongCredentials()
    {
        $data = $this->payload();
        $data['email'] = 'test'; //Email is invalid

        //attempt register
        $response = $this->postJson('/api/v1/signup', $data);
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }

    /**
     * Test that User can not register with existing email
     */
    public function testRegisterWithExistingEmail()
    {
        User::factory()->create(['email' => 'test@gmail.com']);

        //attempt register
        $response = $this->postJson('/api/v1/signup', $this->payload());
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }

    /**
     * Test that User can not with empty fields
     */
    public function testRegisterWithEmptyFields()
    {
        $data = array_keys($this->payload());

        //attempt register
        $response = $this->postJson('/api/v1/signup', $data);
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }

    /**
     * @return array|string[]
     */
    private function payload(): array
    {
        return [
            'first_name' => 'test',
            'last_name' => 'test',
            'username' => 'test',
            'phone_number' => '08131230508',
            'email' => 'test@gmail.com',
            'password' => 'secret123$',
        ];
    }
}
