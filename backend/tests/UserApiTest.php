<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class UserApiTest extends TestCase
{
    private $client;
    private $baseUrl = 'http://localhost:8000/api/users.php';
    private $testUser;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->testUser = [
            'username' => 'testuser_' . time(),
            'password' => 'testpass123'
        ];
    }

    public function testGet_UserList()
    {
        $response = $this->client->get($this->baseUrl . '?action=list');
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertIsArray($data);
    }

    public function testPost_CreateUser()
    {
        $response = $this->client->post($this->baseUrl . '?action=register', [
            'json' => [
                'username' => $this->testUser['username'],
                'password' => $this->testUser['password']
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('User registered!', $data['message']);
    }

    public function testPost_LoginUser()
    {
        // First create a user
        $this->client->post($this->baseUrl . '?action=register', [
            'json' => [
                'username' => $this->testUser['username'],
                'password' => $this->testUser['password']
            ]
        ]);

        // Then try to login
        $response = $this->client->post($this->baseUrl . '?action=login', [
            'json' => [
                'username' => $this->testUser['username'],
                'password' => $this->testUser['password']
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Login successful!', $data['message']);
    }

    public function testPost_FailedLogin()
    {
        try {
            $this->client->post($this->baseUrl . '?action=login', [
                'json' => [
                    'username' => 'nonexistentuser',
                    'password' => 'wrongpassword'
                ]
            ]);
            $this->fail('Expected ClientException was not thrown');
        } catch (ClientException $e) {
            $this->assertEquals(401, $e->getResponse()->getStatusCode());
            $data = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('Invalid username or password', $data['error']);
        }
    }

    public function testRegister_MissingFields()
    {
        try {
            $this->client->post($this->baseUrl . '?action=register', [
                'json' => [
                    'username' => $this->testUser['username']
                    // Missing password
                ]
            ]);
            $this->fail('Expected ClientException was not thrown');
        } catch (ClientException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
            $data = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('Username and password are required', $data['error']);
        }
    }

    public function testRegister_DuplicateUsername()
    {
        // First registration
        $this->client->post($this->baseUrl . '?action=register', [
            'json' => [
                'username' => $this->testUser['username'],
                'password' => $this->testUser['password']
            ]
        ]);

        // Try to register the same username again
        try {
            $this->client->post($this->baseUrl . '?action=register', [
                'json' => [
                    'username' => $this->testUser['username'],
                    'password' => 'differentpassword'
                ]
            ]);
            $this->fail('Expected ClientException was not thrown');
        } catch (ClientException $e) {
            $this->assertEquals(409, $e->getResponse()->getStatusCode());
            $data = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('Username already exists', $data['error']);
        }
    }

    public function testLogin_MissingFields()
    {
        try {
            $this->client->post($this->baseUrl . '?action=login', [
                'json' => [
                    'username' => $this->testUser['username']
                    // Missing password
                ]
            ]);
            $this->fail('Expected ClientException was not thrown');
        } catch (ClientException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
            $data = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('Username and password are required', $data['error']);
        }
    }

    public function testDelete_NonexistentUser()
    {
        $response = $this->client->delete($this->baseUrl . '?action=delete&username=nonexistentuser');
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('User deleted', $data['message']);
    }

    public function testDelete_MissingUsername()
    {
        try {
            $this->client->delete($this->baseUrl . '?action=delete');
            $this->fail('Expected ClientException was not thrown');
        } catch (ClientException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
            $data = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('Username is required', $data['error']);
        }
    }

    public function testInvalidAction()
    {
        try {
            $this->client->get($this->baseUrl . '?action=invalid_action');
            $this->fail('Expected ClientException was not thrown');
        } catch (ClientException $e) {
            $this->assertEquals(400, $e->getResponse()->getStatusCode());
            $data = json_decode($e->getResponse()->getBody(), true);
            $this->assertEquals('Invalid action', $data['error']);
        }
    }

    public function testConcurrentRegistration()
    {
        $requests = [];
        // Create 3 concurrent requests with the same username
        for ($i = 0; $i < 3; $i++) {
            $requests[] = new Request('POST', $this->baseUrl . '?action=register', 
                ['Content-Type' => 'application/json'],
                json_encode([
                    'username' => $this->testUser['username'],
                    'password' => $this->testUser['password']
                ])
            );
        }

        $successCount = 0;
        $conflictCount = 0;

        $pool = new Pool($this->client, $requests, [
            'concurrency' => 3,
            'fulfilled' => function ($response) use (&$successCount) {
                if ($response->getStatusCode() === 201) {
                    $successCount++;
                }
            },
            'rejected' => function ($reason) use (&$conflictCount) {
                if ($reason instanceof ClientException && $reason->getResponse()->getStatusCode() === 409) {
                    $conflictCount++;
                }
            }
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $this->assertEquals(1, $successCount, 'Only one registration should succeed');
        $this->assertEquals(2, $conflictCount, 'Two registrations should fail with conflict');
    }

    protected function tearDown(): void
    {
        // Clean up test user if needed
        try {
            $this->client->delete($this->baseUrl . '?action=delete&username=' . $this->testUser['username']);
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }
} 