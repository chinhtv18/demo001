<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class LoginApiTest extends TestCase
{
    const SUCCESS_STATUS = 'success';
    const ERROR_STATUS = 'fail';
    protected $apiStructure = [
        'status',
        'message',
        'data' => []
    ];

    /**
     * @test
     *
     */
    public function urlReturnOK()
    {
        $response = $this->json('post','/api/login', [
            "email" => "admin@gmail.com",
            "password" => "123"
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testLoginSuccess()
    {
        $response = $this->json('post','/api/login', [
            "email" => "admin@vti.test",
            "password" => "secret"
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::SUCCESS_STATUS);
        $this->assertNotEmpty($responseData['data']['access_token']);
        $this->assertNotEmpty($responseData['data']['userInfo']);
    }

    public function testLoginWithWrongEmail()
    {
        $response = $this->json('post','/api/login', [
            "email" => "admin123@vti.test",
            "password" => "123"
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertArrayNotHasKey('userInfo', $responseData['data']);
    }

    public function testLoginWithWrongPassword()
    {
        $response = $this->json('post', '/api/login', [
            "email" => "admin@vti.test",
            "password" => "1234"
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertArrayNotHasKey('userInfo', $responseData['data']);
    }

    public function testLoginWithNoEmailParam()
    {
        $response = $this->post('/api/login', [
            "password" => "1234"
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message'],'The email field is required.');
    }

    public function testLoginWithNoPasswordParam()
    {
        $response = $this->post('/api/login', [
            "email" => "admin@vti.test",
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message'],'The password field is required.');
    }

    public function testLoginWithInValidEmail()
    {
        $response = $this->post('/api/login', [
            "email" => "adminvti",
            "password" => "secret"
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message'],'The email must be a valid email address.');
    }

    public function testLoginWithInActiveAccount()
    {
        $response = $this->post('/api/login', [
            'email' => 'test002@vti.test',
            'password' => '456'
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->apiStructure);
        $responseData = $response->json();
        $this->assertEquals($responseData['status'], self::ERROR_STATUS);
        $this->assertEquals($responseData['message'],'Your account is inactive');

    }
}
