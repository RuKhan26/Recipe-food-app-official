<?php
use PHPUnit\Framework\TestCase;

class RecipeFoodAppTest extends TestCase {
    private $baseUrl = 'http://localhost:8000/backend/api';
    private $testUsername;

    protected function setUp(): void {
        parent::setUp();
        // Generate a unique username for each test run
        $this->testUsername = 'testuser' . time(); // Removed underscore to ensure valid format
        
        // Verify server is running
        $this->verifyServerIsRunning();
    }

    private function verifyServerIsRunning() {
        $ch = curl_init($this->baseUrl . '/users.php?action=list');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode === 0) {
            $this->markTestSkipped('PHP server is not running. Please start it with: php -S localhost:8000');
        }
    }

    public function testGet_UserList() {
        $response = $this->sendGet("users.php?action=list");
        $this->assertNotFalse($response, 'Failed to get response from server');
        $data = json_decode($response, true);
        $this->assertNotNull($data, 'Failed to decode JSON response: ' . $response);
        $this->assertIsArray($data);
    }

    public function testPost_CreateUser() {
        $data = ['username' => $this->testUsername, 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $this->assertNotFalse($response, 'Failed to get response from server');
        $responseData = json_decode($response, true);
        $this->assertNotNull($responseData, 'Failed to decode JSON response: ' . $response);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User registered successfully', $responseData['message']);
    }

    public function testPost_LoginUser() {
        // First register the user
        $data = ['username' => $this->testUsername, 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $this->assertNotFalse($response, 'Failed to get response from server');
        $responseData = json_decode($response, true);
        $this->assertNotNull($responseData, 'Failed to decode JSON response: ' . $response);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User registered successfully', $responseData['message']);

        // Then try to login
        $response = $this->sendPost("users.php?action=login", $data);
        $this->assertNotFalse($response, 'Failed to get response from server');
        $responseData = json_decode($response, true);
        $this->assertNotNull($responseData, 'Failed to decode JSON response: ' . $response);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Login successful!', $responseData['message']);
    }

    public function testPost_FailedLogin() {
        $data = ['username' => 'nonexistent', 'password' => 'wrongpass'];
        $response = $this->sendPost("users.php?action=login", $data);
        $this->assertNotFalse($response, 'Failed to get response from server');
        $responseData = json_decode($response, true);
        $this->assertNotNull($responseData, 'Failed to decode JSON response: ' . $response);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Invalid username or password', $responseData['error']);
    }

    private function sendGet($endpoint) {
        $ch = curl_init($this->baseUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        $response = curl_exec($ch);
        if ($response === false) {
            $this->fail('Failed to connect to server: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }

    private function sendPost($endpoint, $data) {
        $ch = curl_init($this->baseUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        $response = curl_exec($ch);
        if ($response === false) {
            $this->fail('Failed to connect to server: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }
}
?>
