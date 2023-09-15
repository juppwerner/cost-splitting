Installation with ddev
======================

From the ddev docs page:

"DDEV is an open source tool for launching local web development environments in minutes. It supports PHP, Node.js, and Python (experimental)."

See: https://ddev.readthedocs.io/

Clone this repository from Github/Bitbucket.

Configuration
-------------

In the _config_ directory, create copies of all _*-local.php_sample_ files, so that the filenames end with _*-local.php_.

Enter the database name, database username and password into _config/db-local.php_, as follows:

    <?php
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=db;dbname=db',
        'username' => 'db',
        'password' => 'db',
    ...

Other local settings can be changed in _config/params-local.php_ or _config/web-local.php_

Start the application
---------------------

Start the ddev docker containers:

    $ ddev start

    $ ddev composer install
    $ ddev composer run-script post-create-project-command


Initialize the Database
-----------------------

Run the database migrations:

    $ ddev php yii migrate

and confirm the listed migrations to be run all with _Yes_.


First User Account
------------------

Create a first user, and granting the role _admin_:

    $ ddev php yii user/create USEREMAILADDRESS USERNAME USERPASSWORD admin

by replacing _USEREMAILADDRESS_, _USERNAME_ and _USERPASSWORD_ with your desired values.

You may want to load some sample cost data:

    $ ddev php yii load-sample-data


Browse the Application
----------------------

Finally, access the application in your browser by typing:

    $ ddev launch

For database management, there is Adminer:

    $ ddev launcha -a


Enable the Debug Toolbar and gii
--------------------------------

In order to enable these tools, configure the _modules_ section in your `config/web-local.php` file:

    <?php
    return [
        ...
        'modules' => [
            'debug' => [
                'allowedHosts' => ['cost-splitting.ddev.site'],

            ],
            'gii' => [
                'allowedIPs' => ['127.0.0.1', '::1', '172.19.0.8'],
            ],
        ],
        ...

where your hostname will be shown by the `ddev status`command, and the IP address will be the one as given by the `$_SERVER['REMOTE_ADDR']` variable.

