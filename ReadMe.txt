################################
## Joël Piguet - 2021.11.16 ###
##############################

App/_util:
    create_tables.SQL : créer schémas dans la base de donnée.
    test_populate.SQL : nouvelles entrées user et article pour tester MySQL.

Composer: handle dependency in php
install composer on server.

in terminal:
- cd to app directory
commands:
    composer install
    composer dump-autoload
