Rufaida, Keith ,Shikar

Welcome to our food website. Below will be steps to run our website
All of this was equal work 33/33/33
Please you the last commit prior to the 10 am deadline to use for grading

We decided to create a new website entirely and worked mostly on using our previous knowledge to create a new concept of a recipe website that would give users easier ideas when cooking or just let individuals save their recipes for the future.
We mainly focus on trying to provide an AI feature for our website but keeping the Frontend and Backend structure but expanding to new features that test our knowledge with coding an API


On our project we added feautres including an ai assitant that opens a pop up message bubble upon enterign the website. Any ingrdiesnts can be inputted to the ai assistant and it will return multiple recipes using those ingredints. THen we added a surprise me button that generates randome recipes instantly upon clicking it. There is a recipe jjounral where one can add their own recipes as well as a favorites section where you can save the recipes for returned access. Finally there is a meal plan section that has a weekly diagram where you cna add saved recipes or your own entries to any week day and for a specific meal slot as well.  


**How to run this code using XAMPP on Windows:**

1.) Step one, if you haven't already,y start by downloading XAMPP until you get to the main interface of XAMPP to activate MYSQL and Apache.

2.) We were told in class to swap the port of our Apache to port 8080,but in this case,e please edit your Apache setting to port:8000 as our API_BASE_URL uses that port.

3.) Activate MYSQL and Apache now and wait until it turns green.

4.) After this, please clone this repo and access the file directory and place all the files within this repo inside XAMPP's htdocs files, which is usually in the directory of C://xampp/htdocs. 

5.) After this,s you can go to any of your search engines and search http://localhost:8000/phpmyadmin/. This is particular on , Window,s but if you using a  different OS please search up the right search

6.) After you will need to create a database. You will need to access the database.sql file and copy and paste all of that code.

7.) After go back to myphpadmin that we made you do in step 5 and click MYSQL. This will give you a blank text box, and you will need to copy and paste that code into this and click Go.

8.) Please after make sure that the database was fully created by clickingthe  database If you get any errors, contact us or please refer online.

9.) After this, you should be able to access our website particularly in Windows: http://localhost:8000/frontend/ if you followed step 4 correctly

10.) Here you would be able to register for an account and login to access features of our website.

11.) Particularly you can use our implemented feature our AI chat bot where you can give it recipes to give you dishes.

12.) Other features/ additions are a recipe journal where you can write a recipe and name it ,and save it within your account for later!

13.) Meal plan is a feature where you can set dates of when certain meals that you made in your journal entry will be eaten or made.

P.S.: Some features will be buggy or not work due to some code not working.

**Running Using MAC OS:**

1.) Install Xampp and open from applications. Go to the manage sesrvers tab and start apache and mysql
2.) Open your browser and go to:
http://localhost/phpmyadmin and crate a new database called recipe_app. Go to the SQL tab and paste the table setup from the sql files in the project folder under the backednd/database
3.) Then Put your entire project folder (e.g., recipe-food-app-official) into: /Applications/XAMPP/xamppfiles/htdocs/
4.) 5. Edit the database.php Connection File
In backend/config/database.php, use this: <?php
function getDBConnection() {
    $host = 'localhost';
    $db = 'recipe_app';
    $user = 'root';
    $pass = ''; // Leave blank on XAMPP by default

    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    return new PDO($dsn, $user, $pass);
}
?>
5.) 6. Update JavaScript fetch() URLs
In frontend/script.js, every API call must be absolute: fetch('http://localhost/RecipeApp/backend/api/users.php?action=register', { ... })
So change all the api_base_url to http://localhost/...

6.) 7. Add CORS Headers to Every PHP API File
At the top of users.php, recipes.php, etc., add: header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

7.) Run the frontend by going to your browser and going to http://localhost/recipe-food-app-official/frontend/index.html

8.) Log in and finally test all the functions of the project 
 

**Using the terminal to run website:**
If none of the steps above work, please follow these steps to run the website locally wihtout xampp.

1.) Download PHP and MYSQL from their websites, and please make sure that your terminal or OS system allows the path for both to be access. Please search up how to run php and mysql as this step is far different from using an easier 
program like XAMPP.

I will now separate these steps put please do both of these steps

MYSQL:
1.) Again, make sure you downloaded MYSQL and in the terminal, make sure you're in the repo after cloning the repo from Github.

2.) After, in the terminal,l use  mysql -u root -p < database.sql
But in the worst-case scenario, use 
Or using MySQL Workbench:
Open MySQL Workbench
Connect to your local MySQL server
Open the SQL editor
Copy and paste the contents of database.sql
Execute the script

that should read our database file and configure it for the website

PHP:
1.) Make sure your terminal can read PHP after downloading, and you can test this with php -v

2.) After cloning our repo and cd into the repo and in the folder backend.

3.) ,After in your terminal,l run php -S localhost:8000

4.) Then open the http://localhost:8000 in your browser to see the project running locally.
This should let you now access our website in any web browser and test our website like all the above steps of how to run our website


**Unit Test:** (ai assitance)

After follwing the instructions, the tests were ran, however kept returning errors. With the help of ai, we were able to 
recognize where the errors where taking place and ai made suggestions based on our code, on what errors to correct. The following screen shots show the conversation with ai, after running and analyzing our tests. 

<img width="644" alt="Screenshot 2025-04-30 at 9 41 39 AM" src="https://github.com/user-attachments/assets/2e2b2731-290a-4480-b178-846f10203fac" />

<img width="641" alt="Screenshot 2025-04-30 at 9 41 58 AM" src="https://github.com/user-attachments/assets/c2e44583-c958-4ee6-a33d-63859579405c" />

<img width="636" alt="Screenshot 2025-04-30 at 9 42 20 AM" src="https://github.com/user-attachments/assets/50e6e25c-c06f-4129-b2f5-386924a9cbad" />

<img width="637" alt="Screenshot 2025-04-30 at 9 43 53 AM" src="https://github.com/user-attachments/assets/e94e93b6-0cd8-48c6-8f80-080d319c26ee" />

<img width="638" alt="Screenshot 2025-04-30 at 9 44 17 AM" src="https://github.com/user-attachments/assets/4c473375-dd6e-4a79-bf51-f5882df8f086" />
this final image shows how the ai ran the tests for us each time to assure correct progress. In the final image it shows all 4 tests as passing. 
Here's what we fixed:
- Added a unique username generation using timestamps to prevent conflicts
- Added proper setup with setUp() method to initialize test data
-Modified the login test to ensure user registration before attempting login
-All tests now properly handle JSON responses and check for the correct keys and values
-The test suite now covers:
-Getting the user list
-Creating a new user
-Successfully logging in with valid credentials
-Failed login attempt with invalid credentials
-Each test is independent and uses its own unique test data.

additonal tests ran -- code below 

testRegisterWithMissingFields(): Tests input validation for required fields
Tests registration with missing password
Tests registration with missing username
Verifies appropriate error messages are returned

testRegisterWithInvalidUsername(): Tests username validation rules
Tests username with special characters (should be rejected)
Tests username that's too short (less than 3 characters)
Verifies appropriate error messages are returned

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

    // ... existing code ...

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
One of the additioanl tests failed, however the other 5 were pass (including the 4 required). However due to proceeding errors that the additioanal 2 tests had to get removed from the code. 

