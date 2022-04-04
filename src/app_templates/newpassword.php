<?php
################################
## Joël Piguet - 2021.12.14 ###
##############################

use app\helpers\Util;

$is_alias_address = Util::str_contains($alias, '@');
if ($is_alias_address) {
    //cut '.com' from email address to prevent mail box to set it as hyperlink.
    [$alias_start, $alias_end] = Util::separateAtLast('.', $alias);
}
?>

<div>
    <div>&emsp;Cher<span>
            <?php if ($is_alias_address) { ?>
                <span><?php echo $alias_start ?></span>.<span><?php echo $alias_end ?></span>
            <?php } else { ?>
                <span><?php echo $alias ?></span>
            <?php } ?>
    </div>
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