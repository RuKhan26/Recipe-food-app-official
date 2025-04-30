Rue, Keith ,Shikar

Welcome to our food website. Below will be steps to run our website
All of this was equal work 50/50

We decided to create a new website entirely and worked mostly on using our previous knowledge to create a new concept of a recipe website that would give users easier ideas when cooking or just let individuals save their recipes for the future.
We mainly focus on trying to provide an AI feature for our website but keeping the Frontend and Backend structure but expanding to new features that test our knowledge with coding an API





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



**Using the terminal to run website:**
If none of the steps above work, please follow these step,s but please use this as a last resort.

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

This should let you now access our website in any web browser and test our website like all the above steps of how to run our website


**Unit Test:**





