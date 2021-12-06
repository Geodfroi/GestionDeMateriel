################################
## Joël Piguet - 2021.12.06 ###
##############################

Folders:
    _util:
        create_tables.SQL : créer schémas dans la base de donnée.
        test_populate.SQL : nouvelles entrées user et article pour tester MySQL.

    server:
        server.php: server side app to check expiration dates and send reminder emails.
    src: source files and html templates.
    
    vendor: 
        Composer folder: handle dependency in php
        install composer on server.
        in terminal:
        - cd to app directory
        commands in terminal in root folder:   
            composer install
            composer dump-autoload

Modify const APP_FULL_URL to proper url once the app is online.
