<?php
################################
## Joël Piguet - 2021.12.14 ###
##############################

use app\constants\Route;

?>

<div class="container ">
    <div class="row justify-content-between">
        <a class="col-4" href="<?php echo Route::DEBUG_EMAILS . '?show=newpassword' ?>">regen password email</a>
        <a class="col-4" href="<?php echo Route::DEBUG_EMAILS . '?show=reminder' ?>">reminder email</a>
        <a class="col-4" href="<?php echo Route::DEBUG_EMAILS . '?show=userinvite' ?>">new user invite email</a>
    </div>

    <br><br>
    <div class="border border-1 border-dark"><?php echo $html_template ?></div>
    <div class="my-3 border border-1 border-dark"><?php echo $plaintext ?></div>
</div>