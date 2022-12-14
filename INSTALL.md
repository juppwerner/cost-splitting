INSTALLATION
============

Clone this repository from Github/Bitbucket.

For an installation using Docker, see [INSTALL-docker.md](INSTALL-docker.md).

Using your favorite database manager like PhpMyAdmin or Adminer, create an empty database and a database user granting all priviledges on this database.

Configuration
-------------

In the _config_ directory, create copies of all _*-local.php_sample_ files, so that the filenames end with _*-local.php_.

Enter the database name, database username and password into _config/db-local.php_.

Other local settings can be changed in _config/params-local.php_ or _config/web-local.php_

See also the [Yii2 configuration documentation](https://www.yiiframework.com/doc/guide/2.0/en/concept-configurations).

Initialize the Database
-----------------------

Run database migrations to setup all tables.

Open a console in the root directory of the cloned repository code. Then run:

    $ ./yii migrate

confirming to run all migrations.

### Sample Data

Optionally, you might load sample data (1 cost project, 7 expenses):

    $ ./yii load-sample-data

First User Account
------------------

Create a first user, and granting the role _admin_:

    $ ./yii user/create useremailaddress username userpassword admin

by replacing _useremailaddress_, _username_ and _userpassword_ with the desired values.


Browse the Application
----------------------

Finally, open the application in your browser, e.g.

    http://localhost/cost-splitting/web/index.php

and log in using the newly created user account.
