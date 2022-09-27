<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShopTest extends TestCase
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
     * Test that shop data is empty
     */
    public function testThatShopDataIsEmpty()
    {
        $this->assertEmpty($this->user->shops);
        $response = $this->actingAs($this->user)->getJson('/api/v1/shops');
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
     * Test that shop data is not empty
     */
    public function testShopDataIsNotEmpty()
    {
        //create shop
        $this->createShop();

        //attempt to view all shops
        $response = $this->actingAs($this->user)->getJson('/api/v1/shops');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
            'success',
            'code',
        ]);

        $data = $response->getOriginalContent();
        $this->assertNotEmpty($data['data']);
        $this->assertEquals('Alub ltd', $data['data'][0]['shop_name']);
    }

     /**
      * Test that User can view a single shop
      */
     public function testViewSingleShop()
     {
         $shop = $this->createShop();
         $response = $this->actingAs($this->user)->getJson('/api/v1/shops/'.$shop->id);
         $response->assertStatus(200);
     }

     /**
      * Test that User can not view a single shop that does not exist
      */
     public function testViewSingleShopThatDoesNotExist()
     {
         $response = $this->actingAs($this->user)->getJson('/api/v1/shops/100');
         $response->assertStatus(404);
     }

    /**
     * Test that User can create a shop
     */
    public function testCreateShop()
    {
        $shop = $this->payload();
        $response = $this->actingAs($this->user)->postJson('/api/v1/create-shop', $shop);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data',
            'success',
            'code',
        ]);

        $this->assertDatabaseHas('shops', ['shop_name' => 'Alub ltd']);
    }

    // Test that User can not create a shop with invalid data
    public function testCreateShopWithInvalidData()
    {
        $data = array_keys($this->payload());
        $response = $this->actingAs($this->user)->postJson('/api/v1/create-shop', $data);
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }

    // Test that User can update a shop
    public function testUpdateShop()
    {
        $shop = $this->createShop();
        $data = $this->payload();
        $response = $this->actingAs($this->user)->putJson('/api/v1/update-shop/'.$shop->id, $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
            'success',
            'code',
        ]);
    }

     // Test that User can not update a shop with invalid data
    public function testUpdateShopWithInvalidData()
    {
        $shop = $this->createShop();
        $data = array_keys($this->payload());
        $response = $this->actingAs($this->user)->putJson('/api/v1/update-shop/'.$shop->id, $data);
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }

    // Test that User can not update a shop that does not exist
    public function testUpdateShopThatDoesNotExist()
    {
        $data = $this->payload();
        $response = $this->actingAs($this->user)->putJson('/api/v1/update-shop/1', $data);
        $response->assertStatus(404);
    }

    // Test that User can delete a shop
    public function testDeleteShop()
    {
        $shop = $this->createShop();
        $response = $this->actingAs($this->user)->deleteJson('/api/v1/delete-shop/'.$shop->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
            'success',
            'code',
        ]);
    }

    /**
     * @return array|string[]
     */
    private function payload(): array
    {
        return [
            'shop_name' => 'Alub ltd',
            'shop_description' => 'we dey sell anything',
            'shop_address' => 'alago meji 2',
            'opening_time' => '10:00',
            'closing_time' => '10:30',
        ];
    }

    private function createShop()
    {
        return $this->user->shops()->create([
            'shop_name' => 'Alub ltd',
            'shop_description' => 'we dey sell anything',
            'shop_address' => 'alago meji 2',
            'opening_time' => '10:00',
            'closing_time' => '10:30',
        ]);
    }
}
