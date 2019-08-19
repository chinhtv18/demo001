<?php

namespace Tests\Feature;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;
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
        $staff = $this->getValidStaff();
        $response = $this->json('delete', '/api/staffs/' . $staff->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


    public function testDeleteStaffReturnOK()
    {
        $staff = $this->getValidStaff();
        $token = static::$token;
        $response = $this->json('delete', '/api/staffs/' . $staff->id, [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $staffCheck = Staff::query()->where('id', $staff->id)->get();
        $this->assertEquals($staffCheck->count(), 0);
    }

    public function testDeleteStaffNotFound()
    {
        $token = static::$token;
        $response = $this->json('delete', '/api/staffs/9999', [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message'], 'Staff not found');
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
        $staffList = $this->getStaffList();
        $this->assertEquals($staffList->count(), count($responseData['data']));
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
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $this->assertEqualsCanonicalizing($responseData['data'], $staff->toArray());
    }

    public function testGetStaffNotFound()
    {
        $token = static::$token;
        $response = $this->json('get', '/api/staffs/9999', [], ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
    }

    public function testCreateStaffWithUnAuthorize()
    {
        $response = $this->json('post', '/api/staffs');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateStaffReturnOK()
    {
        $token = static::$token;
        $staffData = $this->getStaffData();
        $response  = $this->json('post', '/api/staffs', $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $staffCheck = Staff::query()->where('email', $staffData['email'])->get();
        $this->assertEquals(1, $staffCheck->count());
    }

    public function testCreateStaffWithEmailIsNull()
    {
        $token = static::$token;
        $staffData = $this->getStaffData();
        unset($staffData['email']);
        $response  = $this->json('post', '/api/staffs', $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['email'], 'The email field is required.');
    }

    public function testCreateStaffWithEmailInvalid()
    {
        $token = static::$token;
        $staffData = $this->getStaffData();
        $staffData['email'] = 'aaaaaa';
        $response  = $this->json('post', '/api/staffs', $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['email'], 'The email must be a valid email address.');
    }
    public function testCreateStaffWithEmailDuplicate()
    {
        $token = static::$token;
        $staffValid = $this->getValidStaff();
        $staffData = $this->getStaffData();
        $staffData['email'] = $staffValid->email;
        $response  = $this->json('post', '/api/staffs', $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['email'], 'The email has already been taken.');
    }

    public function testCreateStaffWithFirstNameIsNull()
    {
        $token = static::$token;
        $staffData = $this->getStaffData();
        $staffData['first_name'] = '';
        $response  = $this->json('post', '/api/staffs', $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['first_name'], 'The first name field is required.');
    }

    public function testCreateStaffWithLastNameIsNull()
    {
        $token = static::$token;
        $staffData = $this->getStaffData();
        $staffData['last_name'] = '';
        $response  = $this->json('post', '/api/staffs', $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['last_name'], 'The last name field is required.');
    }

    public function testUpdateStaffWithUnAuthorize()
    {
        $staff = $this->getValidStaff();
        $response  = $this->json('put', '/api/staffs/' . $staff->id);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateStaffReturnOk()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $id = $staff->id;
        $staffData = $staff->toArray();
        unset($staffData['id']);
        $staffData['first_name'] = $staff['first_name'] . ' update';
        $response  = $this->json('put', '/api/staffs/' . $id, $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $staffCheck = Staff::query()->where('id', $id)->first();
        $this->assertEquals($staffData['first_name'], $staffCheck->first_name);
    }

    public function tesstUpdateStaffReturnOKWithEmail()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $id = $staff->id;
        $staffData = $staff->toArray();
        unset($staffData['id']);
        $staffData['email'] = Str::random(9) . '@vti.test';
        $response  = $this->json('put', '/api/staffs/' . $id, $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $staffCheck = Staff::query()->where('id', $id)->first();
        $this->assertEquals($staffData['first_name'], $staffCheck->first_name);
    }

    public function testUpdateStaffWithEmailIsNull()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $id = $staff->id;
        $staffData = $staff->toArray();
        unset($staffData['id']);
        $staffData['email'] = '';
        $response  = $this->json('put', '/api/staffs/' . $id, $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['email'],'The email field is required.');
    }

    public function testUpdateStaffWithInValidEmail()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $id = $staff->id;
        $staffData = $staff->toArray();
        unset($staffData['id']);
        $staffData['email'] = 'aaaaaa';
        $response  = $this->json('put', '/api/staffs/' . $id, $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['email'],'The email must be a valid email address.');
    }

    public function testUpdateStaffWithEmailDuplicate()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $id = $staff->id;
        $staffData = $staff->toArray();
        unset($staffData['id']);
        $staffDataOther = $this->getValidStaff($id);
        $staffData['email'] = $staffDataOther->email;
        $response = $this->json('put', '/api/staffs/' . $id, $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['email'],'The email has already been taken.');
    }

    public function testUpdateStaffWithFirstNameIsNull()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $id = $staff->id;
        $staffData = $staff->toArray();
        unset($staffData['id']);
        $staffData['first_name'] = '';
        $response  = $this->json('put', '/api/staffs/' . $id, $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['first_name'],'The first name field is required.');
    }
    public function testUpdateStaffWithLastNameIsNull()
    {
        $token = static::$token;
        $staff = $this->getValidStaff();
        $id = $staff->id;
        $staffData = $staff->toArray();
        unset($staffData['id']);
        $staffData['last_name'] = '';
        $response  = $this->json('put', '/api/staffs/' . $id, $staffData, ['Authorization' => $token]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message']['last_name'],'The last name field is required.');
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

    private function getValidStaff($id = null)
    {
        if ($id) {
            $staff = Staff::query()->where('id', '!=', $id)->orderBy('id', 'desc')->first();
        } else {
            $staff = Staff::query()->orderBy('id', 'desc')->first();
        }
        return $staff;
    }

    private function getStaffList()
    {
        return Staff::query()->get();
    }

    private function getStaffData()
    {
        return [
            'email' => Str::random(8) . '@vti.test',
            'first_name' => Str::random(5),
            'last_name' => Str::random(6),
            'sex' => rand(0, 1),
            'address' => 'Ha Noi',
            'birth_day' => Carbon::now()->addYear('-' . rand(20, 25))->format('Y-m-d')
        ];
    }
}
