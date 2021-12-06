<?php
################################
## Joël Piguet - 2021.12.06 ###
##############################

?>

<div>
    <div>Cher
        <?php echo $username ?>,
    </div>
    <br>

    <div>Un compte utilisateur pour l'application [<?php echo $app_name ?>] a été créé pour votre bénéfice.</div>
    <div>Votre mot de passe: </div>
    <br> <br>
    <div> <span style="color:blue;font-size:24px;">&emsp; <?php echo $password ?></span> </div>
    <br><br>
    <div>Une fois <a href="<?php echo $url ?>">connecté</a>, le mot de passe peut être modifié sous l'onglet <i>Profile</i>.
    </div>
</div>