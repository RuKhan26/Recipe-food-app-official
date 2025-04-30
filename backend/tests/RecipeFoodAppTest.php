<?php
use PHPUnit\Framework\TestCase;

class RecipeFoodAppTest extends TestCase {
    private $baseUrl = 'http://localhost:8000/backend/api';
    private $testUsername;

    protected function setUp(): void {
        parent::setUp();
        // Generate a unique username for each test run
        $this->testUsername = 'testuser_' . time();
    }

    public function testGet_UserList() {
        $response = $this->sendGet("users.php?action=list");
        $this->assertNotFalse($response);
        $data = json_decode($response, true);
        $this->assertIsArray($data);
    }

    public function testPost_CreateUser() {
        $data = ['username' => $this->testUsername, 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User registered successfully', $responseData['message']);
    }

    public function testPost_LoginUser() {
        // First register the user
        $data = ['username' => $this->testUsername, 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User registered successfully', $responseData['message']);

        // Then try to login
        $response = $this->sendPost("users.php?action=login", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Login successful!', $responseData['message']);
    }

    public function testPost_FailedLogin() {
        $data = ['username' => 'nonexistent', 'password' => 'wrongpass'];
        $response = $this->sendPost("users.php?action=login", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Invalid username or password', $responseData['error']);
    }

    public function testRegisterWithMissingFields() {
        // Test with missing password
        $data = ['username' => $this->testUsername];
        $response = $this->sendPost("users.php?action=register", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Username and password are required', $responseData['error']);

        // Test with missing username
        $data = ['password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Username and password are required', $responseData['error']);
    }

    public function testRegisterWithInvalidUsername() {
        // Test with username containing special characters
        $data = ['username' => 'test@user#123', 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Username can only contain letters, numbers, and underscores', $responseData['error']);

        // Test with username that's too short
        $data = ['username' => 'ab', 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Username must be between 3 and 20 characters', $responseData['error']);
    }

    private function sendGet($endpoint) {
        $ch = curl_init($this->baseUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function sendPost($endpoint, $data) {
        $ch = curl_init($this->baseUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
?>
