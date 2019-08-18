<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StaffApiTest extends TestCase
{
    static $_init = false;

    static $token = null;

    public function setUp():void
    {
        parent::setUp();
        if (static::$_init === false) {
            $token = $this->getToken();
            \Artisan::call('db:seed', [
                '--class' => 'StaffsTableSeeder'
            ]);
            static::$token = $token;
            static::$_init = true;
        }
    }

    public function testDeleteStaffWithUnAuthorize()
    {

    }

    public function testDeleteStaffWithAuthorize()
    {

    }

    public function testDeleteStaffNotFound()
    {

    }

    public function testDeleteStaffReturnOK()
    {
        
    }

    public function testGetStaffListWithUnAuthorize()
    {
        $response = $this->json('get', '/api/staffs');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetStaffListWithAuthorize()
    {
        $token = static::$token;
        $response = $this->json('get', 'api/staffs', [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testGetStaffListReturnOk()
    {
        $token = static::$token;
        $response = $this->json('get', 'api/staffs', [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $this->assertEquals(3, count($responseData['data']));
    }

    public function testGetStaffDetailWithUnAuthorize()
    {
        $staff = $this->getValidStaff();
        $response = $this->json('get', '/api/staffs/' . $staff->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetStaffDetailWithAuthorize()
    {
        $token = static::$token;
        $response = $this->json('get', '/api/staffs/123', [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);

    }

    public function testGetStaffDetailReturnOK()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $response = $this->json('get', '/api/staffs/' . $staff->id, [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $this->assertEqualsCanonicalizing($responseData['data'], $staff->toArray());
    }

    public function testGetStaffNotFound()
    {
        $token = static::$token;
        $response = $this->json('get', '/api/staffs/9999', [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
    }

    public function testCreateStaffWithUnAuthorize()
    {

    }

    public function testCreateStaffWithAuthorize()
    {

    }

    public function testCreateStaffWithEmailIsNull()
    {

    }

    public function testCreateStaffWithEmailInvalid()
    {

    }
    public function testCreateStaffWithEmailDuplicate()
    {

    }

    public function testCreateStaffWithFirstNameIsNull()
    {

    }

    public function testCreateStaffWithLastNameIsNull()
    {

    }

    public function testCreateStaffReturnOk()
    {

    }

    public function testUpdateStaffWithUnAuthorize()
    {

    }

    public function testUpdateStaffWithAuthorize()
    {

    }

    public function testUpdateStaffWithEmailIsNull()
    {

    }

    public function testUpdateStaffWithInValidEmail()
    {

    }

    public function testUpdateStaffWithEmailDuplicate()
    {

    }

    public function testUpdateStaffWithFirstNameIsNull()
    {

    }
    public function testUpdateStaffWithLastNameIsNull()
    {

    }

    public function testUpdateStaffReturnOk()
    {

    }

    public function getToken()
    {
        $response = $this->json('post','/api/login', [
            "email" => "admin@vti.test",
            "password" => "secret"
        ]);
        $responseData = $response->json();
        $token = $responseData['data']['access_token'];
        return $token;
    }

    public function getValidStaff()
    {
        $staff = Staff::query()->orderBy('id', 'desc')->first();
        return $staff;
    }
}
