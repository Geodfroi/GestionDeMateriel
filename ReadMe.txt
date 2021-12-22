################################
## Joël Piguet - 2021.12.22 ###
##############################

Folders:
    config.json: variables controlant le comportement de l'application, serveur our tests.

    __launcher:
        bat files pour lancer l'application web, le serveur ou les tests.

    _util:
        create_mysql_tables.SQL : créer schémas dans la base de donnée.
        debug_populate.SQL : nouvelles entrées user et article pour tester MySQL.

    server:
        server.php: server side app to check expiration dates and send reminder emails.
        src: source files and html templates.

    web:
        Web app folder.
    
    vendor: 
        Composer folder: handle dependency in php
        install composer on server

    tests: 
        phpunit tests
        https://phpunit.readthedocs.io/en/9.5/index.html
        http://nlslack.com/getting-started-with-phpunit-7-using-composer/
        run in terminal: ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests 

Modify const APP_FULL_URL to proper url once the app is online.
