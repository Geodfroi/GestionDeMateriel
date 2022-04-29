<?php
################################
## Joël Piguet - 2022.04.29 ###
#############################

use app\helpers\Util;

$today = Util::stripTimeComponent(new DateTime());
?>
<div class="container">
    <div class="h3">
        <span> <?php echo APP_NAME . ' : ' . $today->format('d-m-Y') ?></span>
        <div>................................................</div>
    </div>

    <?php if ($already_checked) { ?>
        <div class="h4">La sauvegarde et l'envoi des emails ont déjà été effectués aujourd'hui.</div>
    <?php } else { ?>

        <!-- <?php if ($backup_success) { ?>
            <div class="h4">Backup de la base de donnée au format ".sqlite" dans le dossier "_backups".</div>
        <?php } ?> -->
        <!-- <div>................................................</div> -->
        <div class="h4">Commence le script server.</div>
        <div class="h4">Vérifie les emails de péremptions à envoyer.</div>
        <div>................................................</div>

        <?php if (count($reminders) > 0) {
            foreach ($reminders as $user_email) {
                $user = $user_email->getUser() ?>
                <div class="h5 mt-3">
                    <span>Utilisateur</span>
                    <span><?php echo $user->getLoginEmail() ?></span>
                    <span>; Dernier login: </span>
                    <span><?php echo $user->getLastLogin()->format('d-m-Y H:i:s') ?></span>
                </div>
                <?php if ($user_email->hasEmails()) {
                    if ($user_email->wasSent()) { ?>
                        <ul class="mx-3">
                            <?php foreach ($user_email->getEmails() as $array) { ?>
                                <li>
                                    <?php echo $array['article']; ?>
                                </li>
                            <?php } ?>
                        </ul>
                        <div class="mx-3"> Rappels envoyé.</div>
                    <?php } else {  ?>
                        <div class="h5">Erreur lors de l'envoi des emails de rappels à cet utilisateur</div>
                    <?php }
                } else {  ?>
                    <div class="mx-3">Pas de notices de péremptions à envoyer à cet utilisateur aujourd'hui.</div>
        <?php }
            }
        } ?>
    <?php } ?>
</div>