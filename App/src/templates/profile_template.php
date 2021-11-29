<?php
################################
## Joël Piguet - 2021.11.29 ###
##############################

use helpers\TUtil;

?>

<div class="container">

    <div class="row">
        <div class="col-12">
            <?php if (isset($alert['type'])) { ?>
                <div class='text-center alert alert-<?php echo $alert['type'] ?> alert-dismissible fade show' role='alert'><?php echo $alert['msg'] ?>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            <?php } ?>
        </div>
    </div>


    <!-- Profile change list -->
    <?php if ($display == 0) { ?>

        <div class="row"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto mt-4" href="<?php echo PROFILE . '?change_password=1' ?>">Changer mon mot de passe</a></div>

        <div class="row" data-bs-toggle="tooltip" title="Il n'est pas possible de changer l'adresse de login, mais l'adresse e-mail par laquelle l'application vous contacte peut être modifiée." data-bs-placement="bottom"> <a class=" btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo PROFILE . '?add_email=1' ?>">Ajouter une adresse de contact</a></div>

        <div class="row" data-bs-toggle="tooltip" title="Par défault, l'application vous averti 2 semaines avant qu'un article arrive à péremption." data-bs-placement="bottom">
            <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo PROFILE . '?modify_delay=1' ?>">Modifier le délai de contact</a>
        </div>
    <?php } else { ?>

        <?php if ($display == 1) { ?>
            <!-- Modify password form -->
            <form method="post" action=<?php echo PROFILE ?>>
                <div class="row mt-4 mb-3">
                    <div class="col-12 col-md-8 mx-auto">
                        <input name="password" type="password" id="password" class="form-control <?php echo TUtil::showValid($errors, $values, 'password') ?>" value=<?php echo TUtil::escape($values['password']) ?>>

                        <?php if (isset($errors['password'])) { ?>
                            <div class='invalid-feedback'><?php echo $errors['password'] ?> </div>
                        <?php } else { ?>
                            <div id="id-descr" class="form-text">Entrer votre nouveau mot de passe.</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-8 mx-auto">
                        <input name="password-repeat" type="password" id="password-repeat" class="form-control <?php echo TUtil::showValid($errors, $values, 'password-repeat')  ?>" value=<?php echo TUtil::escape($values['password-repeat']) ?>>
                        <?php if (isset($errors['password-repeat'])) { ?>
                            <div class='invalid-feedback'><?php echo $errors['password-repeat'] ?> </div>
                        <?php } else { ?>
                            <div id="id-descr" class="form-text">Entrer votre nouveau mot de passe une seconde fois.</div>
                        <?php } ?>
                    </div>
                </div>

                <div class="row">
                    <button type="submit" name="new-password" class="btn btn-primary col-12 col-md-6 mx-auto mb-2">Modifier</button>
                </div>
            </form>

        <?php } elseif ($display == 2) { ?>
            add email
        <?php } elseif ($display == 3) { ?>
            modify delay
        <?php } ?>

        <div class="row"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo PROFILE ?>">Revenir</a></div>
    <?php }  ?>
</div>




<!-- <a href="" class="link-secondary text-info ms-2"><i class="bi bi-usb-plug" role="img" style="font-size: 1.2rem;" aria-label="connect-as" data-bs-toggle="tooltip" title="Se connecter en tant que  "></i></a> -->

<div>

    <div class=" div">Profile template: </div>
    <div class="div">TODO - changer de password.</div>
    <div class="div"> TODO - ajouter une adresse email privée de contact.</div>
    <div class="div"> TODO - changer le délai avant que les mails de notification arrivent (défaut 2 semaines).</div>

</div>