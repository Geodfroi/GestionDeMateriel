<?php
################################
## Joël Piguet - 2021.12.13 ###
##############################

?>

<div>
    <div>&emsp;Madame, Monsieur,</div>
    <br>
    <div>&emsp;Un compte utilisateur pour l'application <a href="<?php echo $url ?>"><?php echo $app_name ?></a> a été créé pour votre bénéfice.</div>
    <br>
    <table>
        <tr>
            <td>&emsp;Votre login: </td>
            <td>&emsp;&emsp;&emsp;</td>
            <td><span style="color:blue;font-size:16px;">&emsp;<?php echo $login ?></span></td>
        </tr>
        <tr>
            <td>&emsp;Votre mot de passe:</td>
            <td>&emsp;&emsp;&emsp;</td>
            <td><span style="color:blue;font-size:16px;">&emsp;<?php echo $password ?></span></td>
        </tr>
    </table>
    <br><br>
    <div>&emsp;Une fois connecté, votre mot de passe peut être modifié sous l'onglet <i>Profile</i>.
    </div>
</div>