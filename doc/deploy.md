Deploy auf www.diggin-data.de
=============================

This is intended to b erun using the comman line in php-git-bundle.

Install composer
----------------

* see https://getcomposer.org/download/

Set GitHub token

* https://github.com/settings/tokens

export COMPOSER_HOME=. && /usr/bin/php74 php composer.phar config github-oauth.github.com ghp_TOKEN...

export COMPOSER_HOME=. && /usr/bin/php74 composer.phar install
