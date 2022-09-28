<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()
            ->create(['password' => Hash::make('secret123$'), 'role' => 'vendor']);
    }

    /**
     * Test that appointment data is empty
     */
    public function testThatAppointmentDataIsEmpty()
    {
        $this->assertEmpty($this->user->appointments);
        $response = $this->actingAs($this->user)->getJson('/api/v1/appointments');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
            'success',
            'code',
        ]);

        $data = $response->getOriginalContent();
        $this->assertEmpty($data['data']);
    }

    /**
     * Test that appointment data is not empty
     */
    public function testAppointmentDataIsNotEmpty()
    {
        //create appointment
        $this->createAppointment();

        //attempt to view all appointments
        $response = $this->actingAs($this->user)->getJson('/api/v1/appointments');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
            'success',
            'code',
        ]);

        $data = $response->getOriginalContent();
        $this->assertNotEmpty($data['data']);
    }

    private function createAppointment()
    {
        return $this->user->appointments()->create([
            'date' => '2021-05-01',
            'time' => '11:00',
        ]);
    }
}
