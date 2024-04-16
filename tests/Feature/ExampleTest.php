<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    // public function test_register_api_can_be_accessed()
    // {
    //     $user = User::factory()->make();
    
    //     $data = [
    //         'name' => $user->name,
    //         'email' => $user->email,
    //         'password' => '123456789',
    //         'c_password' => '123456789'
    //     ];
    //     $response = $this->postJson('/api/register', $data);
    //     $response->assertStatus(200);
    // }

    public function test_login_api_can_be_accessed()
    {
        $credentials = [
            'email' => 'KN@gmail.com',
            'password' => '123456789'
        ];
        $response = $this->postJson('/api/login', $credentials);
        $response->assertStatus(200);
    }


}
