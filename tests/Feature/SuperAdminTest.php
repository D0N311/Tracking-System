<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    
    private $token;

    public function setUp(): void
    {
        parent::setUp();

        $credentials = [
            'email' => 'superadmin@gmail.com',
            'password' => '123456789'
        ];

        $response = $this->postJson('/api/login', $credentials);

        $this->token = $response->json()['data']['token'];
    }

    public function test_add_company()
    {   
        $company = Company::factory()->make();
        $data = [
            'company_name' => $company->company_name,
            'description' => $company->description,
            'location' => $company->location,
            'admin_id' => ''
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/superadmin/addCompany', $data);

        $response->assertStatus(201);
    }

    public function test_company_index()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/superadmin/companyIndex');

        $response->assertStatus(200);
    }

    public function test_company_admin_index()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/superadmin/adminIndex');

        $response->assertStatus(200);
    }
    

}
