<?php
################################
## Joël Piguet - 2022.02.08 ###
##############################

?>

<div class="container">
</div>

<br><br>
<div>
    <textarea style="width: 100%; height : 100vh" name="" id="" cols="30" rows="10">
    #############################
    ##Joël Piguet - 2022.03.10##
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

    web:
    Web app folder.

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

    Fake credentials to try the app:
    login noel.biquet@gmail.com
    password: 123123

    Debug mode:
    verbose logging in local/logs.
    Also, in debug mode all emails are sent to the development email instead of user emails.

    functionality

    - Sqlite implementation on top of mysql for local testing (change in config.json).
    - Use own gmail address to send reminder emails.
    - Server backup to sqlite local db each day.
    - App was created to work on both desktop and smartphone. Use chrome or edge devKit to switch to phone view to see the result.

    Reminder: modify const APP_FULL_URL to proper url once the app is online.
    </textarea>

    <!-- <div>TODO::</div>
    <br>
    <div>Bug with expiration date before filter in sqlite</div>
    <div>CONTACT: create contact page</div>
    <div>ADMIN - access user contact posts.</div>
    <div>ARTICLES: filter with created by</div>
    <div>LOGIN: replace with proper favicon</div>
    <div>publich to hiroku ?</div>
    <div>publich to microsoft azure ?</div> -->
</div>