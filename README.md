#############################
##Joël Piguet - 2022.03.11##
###########################

PHP v7.4.25 with Composer, coded with VSCode.

Folders:
config.php: variables controlant le comportement de l'application, le serveur ou les tests.

    __launcher:
        bat files to launch web app, server app or tests. Open in

    _util:
        mysql_populate.SQL : utiliser dans mySQL pour créer schémas et nouvelles entrées pour tester MySQL.

    server:
        server.php: server side app to check expiration dates and send reminder emails.
        src: source files and html templates.

    vendor:
        Composer folder: handle dependency in php.
        install composer on server

    tests:
        phpunit tests
        https://phpunit.readthedocs.io/en/9.5/index.html
        http://nlslack.com/getting-started-with-phpunit-7-using-composer/
        run in terminal: ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests

Setup mySQL for testing:
Deploy dependencies through composer if not already done.
Fill in mysql and email info under src/constants/privatesettings.php

Fake credentials to use the app:
login noel.biquet@gmail.com
password: 123123

Debug mode:
verbose logging in local/logs.
Also, in debug mode all emails are sent to the development email instead of user emails.

Extra functionality
Sqlite implementation on top of mysql for local testing (change in config.json).
Use own gmail address to send reminder emails.
Server backup to sqlite local db each day.
App was created to work on both desktop and smartphone. Use chrome or edge devKit to switch to phone view to see the result.
