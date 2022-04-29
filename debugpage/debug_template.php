<?php
################################
## Joël Piguet - 2022.04.29 ###
##############################

use app\constants\Route;
?>

<div class="container">
</div>

<br><br>
<a href="<?php echo Route::DEBUG_EMAILS ?>">Voir templates des emails de rappels.</a>
<br><br>
<a href="<?php echo Route::SERVER ?>">Lancer la distribution des emails de rappels.</a>
<br><br>
<div>
    <textarea style="width: 100%; height : 40vh" name="" id="" cols="30" rows="10" readonly>
    #############################
    ##Joël Piguet - 2022.04.05##
    ###########################

    PHP v7.4.25 with Composer, coded with VSCode.

    functionality

    - Sqlite implementation on top of mysql for local testing (change in config.json).
    - Use own gmail address to send reminder emails (no longer functioning).
    - Server backup to sqlite local db each day.
    - App was created to work on both desktop and smartphone. Use chrome or edge devKit to switch to phone view to see the result.
    </textarea>

    <div>TODO::</div>
    <br>
    <div>Bug with expiration date before filter in sqlite</div>
</div>