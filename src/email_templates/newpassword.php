<?php
################################
## Joël Piguet - 2021.12.14 ###
##############################

?>

<div>
    <div>&emsp;Cher<span style="text-decoration:none;"><?php echo $username ?></span>,</div>
    <br>

    <div>&emsp;Votre mot de passe pour vous connecter à l'application <a href="<?php echo $url ?>"><?php echo $app_name ?></a> a été réinitialisé. </div>
    <br> <br>

    <table>
        <tr>
            <td>&emsp;&emsp;Votre nouveau mot de passe : </td>
            <td>&emsp;&emsp;&emsp;</td>
            <td><span style="color:blue;font-size:16px;">&emsp;<?php echo $password ?></span></td>
        </tr>
    </table>
    <br> <br>
    <div>&emsp;Une fois connecté, le mot de passe peut être modifié sous l'onglet <i>Profile</i>.
    </div>
</div>