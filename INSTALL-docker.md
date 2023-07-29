Installation with Docker
========================

Clone this repository from Github/Bitbucket.

Install the application dependencies:

    docker-compose run --rm frontend composer install

Configuration
-------------

Prepare the config files:

* See corresponding chapter in [INSTALL](INSTALL.md)

Adjust the components['db'] configuration in _config/db-local.php_ accordingly.

    'dsn' => 'mysql:host=mysql;dbname=costsplitting',
    'username' => 'costsplitting',
    'password' => 'costsplitting',

Create a file _mysql_root.txt_, and put in the MySQL root password.

Create a file _mysql_user.txt_, and put in the MySQL database user password (same as above).


Docker networking creates a DNS entry for the host mysql available from your frontend container.

For more information about Docker setup please visit the guide.

Start the application:

    docker-compose up -d

Initialize the Database
-----------------------

Run the database migrations:

    docker-compose run --rm frontend yii migrate

confirming to run all migrations.

First User Account
------------------

Create a first user, and granting the role _admin_:

    docker-compose run --rm frontend yii user/create USEREMAILADDRESS USERNAME USERPASSWORD admin

by replacing _USEREMAILADDRESS_, _USERNAME_ and _USERPASSWORD_ with your desired values.

You may want to load some sample cost data:

    docker-compose run --rm frontend yii load-sample-data

Browse the Application
----------------------

Finally, access the application in your browser by opening:

* Frontend: http://127.0.0.1:8080
* Adminer Database Management: http://127.0.0.1:8081/?server=mysql&db=costsplitting
