## JIRA REST API Interface
The code list above will interface with JIRA Restful API. It will download the issues and save them to a database. It also build the URL string and allows for many other parameters. It comes with two classes and some example files:
* Jira.php - Script to be executed.
* Jira.sql - My SQL file that is used to save the data to.
* JiraData.class.php - Class that contains the REST interface.
* JiraDB.class.php - Class that contains the database interface.
* JiraLogger.class.php - Class that helps log results/failures


## How To Use
Depending on your needs, there are a few steps that you will have to preform. The only files that may need to be modified are the Jira.php and Jira.sql files. These files contain the core calls to the class and most of the dynamic data is stored in the database.

# Setting up the database
Most of the code is written in a dynamic way and pulls from a MySQL database. If you’re using something different, the code may need to be modified to accommodate your database. Otherwise the Jira.sql file has all the information you will need to set up the database and the values that need to be in setup.

# Setting up the Script
Once the database is setup you can follow the example in Jira.php to get your particular instance up and running.
