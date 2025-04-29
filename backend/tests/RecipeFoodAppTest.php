
<?php
use PHPUnit\Framework\TestCase;

class RecipeFoodAppTest extends TestCase {
    private $baseUrl = "http://localhost/recipe-food-app/backend/api/";

    public function testGet_UserList() {
        $response = file_get_contents($this->baseUrl . "users.php");
        $this->assertNotFalse($response);
    }

    public function testPost_CreateUser() {
        $data = ['username' => 'testuser', 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=register", $data);
        $this->assertStringContainsString('User registered', $response);
    }

    public function testPost_LoginUser() {
        $data = ['username' => 'testuser', 'password' => 'testpass'];
        $response = $this->sendPost("users.php?action=login", $data);
        $this->assertStringContainsString('Login successful', $response);
    }

    public function testPost_FailedLogin() {
        $data = ['username' => 'nonexistent', 'password' => 'wrongpass'];
        $response = $this->sendPost("users.php?action=login", $data);
        $this->assertStringContainsString('Login failed', $response);
    }

    private function sendPost($endpoint, $data) {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
?>
