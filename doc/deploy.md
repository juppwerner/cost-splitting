Install composer

* see https://getcomposer.org/download/

Set GitHub token

* https://github.com/settings/tokens

export COMPOSER_HOME=. && php composer.phar config github-oauth.github.com ghp_TOKEN...

export COMPOSER_HOME=. && php composer.phar install
