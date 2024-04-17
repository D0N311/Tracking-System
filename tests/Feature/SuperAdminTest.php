<?php

namespace Tests\Feature;

use App\Models\Company;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    
    private $token;
    private $email = 'Ld7LUTpHCO@gmail.com';
    private $id = 7;

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

   public function test_activate_admin()
    {
        $data = [
            'admin' => $this->email,
            'role' => 'Admin'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/superadmin/activateAdmin', $data);

        $response->assertStatus(200);
    }

    public function test_set_admin()
    {
        $data = [
            'email' => $this->email,
            'company_id' => 3
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/superadmin/setAdmin', $data);

        $response->assertStatus(200);
    }

    public function test_remove_admin()
    {
        $data = [
            'company_id' => 3,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/superadmin/removeAdmin', $data);

        $response->assertStatus(200);
    }

    public function test_deactivate_admin()
    {
        $data = [
            'admin_id' => $this->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/superadmin/deactivateAdmin', $data);

        $response->assertStatus(200);

    }

}
