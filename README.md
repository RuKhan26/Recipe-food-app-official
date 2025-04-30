Rue,Keith,Shikar

Welcome to our food website. Please check at the Directory skeleton at the very bottom if you get lost in any part of the instructions when refering to any files but please follow below with the correct OS.
All of this was equal work 50/50

We decided to create a new website entirley and worked on mostly on using our previous knowledge to make a new concept of a recipe website for giving users easier to get ideas when cooking or just letting individuals save their recipes for the future.
We mainly focus trying to provide a AI feature for our website but keeping the Frontend and Backend structure but expanding to new features that test our knowledge with coding an API





**How to run this code using XAMPP on Windows:**
1.) Step one if you haven't already start by downloading XAMPP until you get to the main interface of XAMPP to activate MYSQL and Appache.

2.) We were told in class to swap the port of our Appache to port 8080 but in this case please edit your Appache setting to port:8000 as our API_BASE_URL uses that port.

3.) Activate MYSQL and Appache now and wait until it turns green.

4.) After this please Clone this repo and access the file directory and place all the files within this repo inside XAMPP's htdocs files that is usally in the directory of C://xampp/htdocs. 

5.) After this you can go to any of your search engines and search http://localhost:8000/phpmyadmin/ this is particular on windows but if you using different OS please search up the right search

6.) After you will need to create a database. You will need to access the database.sql file and copy paste all of that code.

7.) After go back to myphpadmin that we made you do in step 5 and click MYSQL. This will give you a blank text box and you will need to copy and paste that code into this and click Go.

8.) Please after make sure that the database was fully created by clicking database if you get any errors contact us or please refer online.

9.) After this you should be able to access our website particularlly in windows: http://localhost:8000/frontend/ if you followed step 4 correctly

10.) Here you would be able to register for an account and login to access features of our website.

11.) Particularlly you can use our implemented feature our AI chat bot where you can give it recipes to give you dishes.

12.) Other features/ additions is a recipe journal where you can write a recipe and name it and save it within your account for later!

13.) Meals plan is a feature where you can set dates of when certain meals that you made in your journal entry will be eaten or made.

p.s: Some features will be buggy or not working due to some code not working.

**Running Using MAC OS:**



**Using the terminal to run website:**
If none of those the steps above work please follow these steps but please use this as a last resources.

1.) Download PHP and MYSQL from their websites and please make sure that your terminal or OS system allows the path for both to be access. Please search up how to run php and mysql as this step is far different from using a easier 
program like XAMPP.

I will now sepearate these steps put please do both of these steps

MYSQL:
1.) Again make sure you downloaded MYSQL and in the terminal make sure your cd in the repo after cloning the repo from Github.

2.) After in the terminal use  mysql -u root -p < database.sql
But in the worst case scenario use 
Or using MySQL Workbench:
Open MySQL Workbench
Connect to your local MySQL server
Open the SQL editor
Copy and paste the contents of database.sql
Execute the script

that should read our database file and configured it for the website

PHP:
1.) Make sure your terminal can read php after downloading and you can test this with php -v

2.) After clone our repo and cd into the repo and in the folder backend.

3.) after in your terminal run php -S localhost:8000

this should let you now access our website in any web browser and test our website like all the above steps of how to run our website


**Unit Test:**




Recipe-food-app-official-7/
├── .git/
├── vendor/
├── frontend/
│   ├── style.css
│   ├── styles.css
│   ├── script.js
│   ├── journal.html
│   ├── meal_plans.html
│   ├── images/
│   ├── index.html
│   └── favorites.html
├── backend/
│   ├── vendor/
│   ├── tests/
│   ├── index.php
│   ├── test_db.php
│   ├── database/
│   ├── config/
│   │   ├── setup.sql
│   │   ├── db.php
│   │   ├── setup.php
│   │   ├── database.sql
│   │   └── database.php
│   ├── api/
│   │   ├── users.php
│   │   ├── meal_plans.php
│   │   ├── recipes.php
│   │   ├── journal.php
│   │   ├── favorites.php
│   │   ├── google_search.php
│   │   └── ai_assistant.php
│   ├── composer.lock
│   └── composer.json
├── composer.lock
├── composer.json
├── database.sql
├── README.md
└── .gitattributes
