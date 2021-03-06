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

    <div>&emsp;Les articles suivants approchent de leurs dates de péremption: </div>
    <br>

    <table role=" presentation" style="width: 100%; max-width: 800px; margin-left: 30px;">
        <thead style="border-bottom: 10px solid white;">
            <tr>
                <th style="width: 30%; text-align: left; color:blue">Article</th>
                <th style="width: 30%; text-align: left; color:blue">Emplacement</th>
                <th style="width: 20%; text-align: left; color:blue">Délai</th>
                <th style="width: 20%; text-align: left; color:blue">Date de péremption</th>
            </tr>
        </thead>
        <tbody>

            <?php $indice = 1;
            for ($n = 0; $n < count($reminders); $n++) { ?>
                <?php $reminder = $reminders[$n];
                ?>
                <!-- set background of even rows in light grey -->
                <tr <?php echo $n % 2 === 0 ? "style='background: #eee'" : '' ?>>
                    <td>
                        <?php echo $reminder['article']->getArticleName(); ?>
                        <?php if ($reminder['article']->getComments()) { ?>
                            <sup><span style="font-size: 10px;"><?php echo $indice ?></span></sup>
                        <?php
                            $indice += 1;
                        } ?>
                    </td>
                    <td> <?php echo $reminder['article']->getLocation() ?></td>
                    <td <?php echo $reminder['delay'] === 3 ? "style='color:red;'" : '' ?>>
                        <?php echo $reminder['delay'] ?> jours.</td>
                    <td><?php echo $reminder['article']->getExpirationDate()->format('d.m.Y') ?></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
    <br>

    <?php $indice = 1; ?>
    <?php foreach ($reminders as $reminder) { ?>
        <?php if ($reminder['article']->getComments()) { ?>
            <div style="font-size: 12px;"> <?php echo "{$indice}. {$reminder['article']->getComments()}" ?></div>

    <?php $indice += 1;
        }
    } ?>
    <br>
    <div>&emsp;Vous pouvez modifier le délai de rappel dans l'onglet <i>Profil</i> au sein de <a href="<?php echo $url ?>">l'application.</a></div>
</div>