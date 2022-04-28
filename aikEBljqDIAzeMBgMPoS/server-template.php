<?php
################################
## Joël Piguet - 2022.04.28 ###
##############################

?>

<div class="container">
    <?php if ($backup_success) { ?>
        <div class="h4">Backup de la base de donnée au format ".sqlite" dans le dossier "_backups."'</div>
    <?php } ?>
    <div>................................................</div>
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
                if ($user_email->wasSent()) {
                    foreach ($user_email->getEmails() as $array) { ?>
                        <div class="mx-3">
                            <?php echo $array['article']; ?>
                        </div>
                    <?php } ?>
                    <div class="mx-3"> rappels envoyé.</div>
                <?php } else {  ?>
                    <div class="h5">Erreur lors de l'envoi des emails de rappels à cet utilisateur</div>
                <?php }
            } else {  ?>
                <div class="h5">Pas de notices de péremptions à envoyer à cet utilisateur aujourd'hui.</div>
    <?php }
        }
    } ?>
</div>