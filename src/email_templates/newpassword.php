<?php
################################
## Joël Piguet - 2021.12.05 ###
##############################

?>

<div>
    <div>Cher
        <?php echo $username ?>,
    </div>
    <br>

    <div>Voici votre nouveau mot de passe pour vous connecter à l'application [<?php echo $app_name ?>]: </div>
    <br> <br>
    <div> <span style="color:blue;font-size:24px;">&emsp; <?php echo $password ?>.</span> </div>
    <br><br>
    <div>Une fois connecté, le mot de passe peut être modifié sous l'onglet <i>Profile</i>.
    </div>
</div>