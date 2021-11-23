<?php
################################
## Joël Piguet - 2021.11.22 ###
##############################

use helpers\TemplateUtil;
use routes\Routes;

?>

<div class="container">
    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto mb-5">
            <form method="post" action="/login">

                <!-- display new password info alert -->
                <?php if (isset($alerts['new-password'])) { ?>
                    <div class='alert alert-info alert-dismissible fade show' role='alert'>Un nouveau mot de passe a été envoyé à '<?php echo $values['email'] ?>'
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                <?php } ?>

                <!-- user logout info alert -->
                <?php if (isset($alerts['disconnect'])) { ?>
                    <div class='alert alert-info alert-dismissible fade show' role='alert'>L'usager précédent s'est déconnecté.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>"
                <?php } ?>

                <label class="h4 m-4">Formulaire d'identification</label>
                <div class="mb-3">
                    <label for="form-email" class="form-label">Adresse e-mail</label>
                    <input id="form-email" type="email" name='email' aria-describedby="id-descr" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'email') ?>
                        " value=<?php echo TemplateUtil::escape($values['email']); ?>>

                    <div class='invalid-feedback'><?php echo $errors['email'] ?> </div>

                    <?php if (!isset($errors['email'])) { ?>
                        <div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>
                    <?php } ?>

                </div>
                <div class="mt-3 mb-3">
                    <label for="form-password" class="form-label">Mot de passe</label>
                    <input id="form-password" type="password" name='password' class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'password') ?>">

                    <div class='invalid-feedback'><?php echo $errors['password'] ?> </div>

                </div>
                <button type="submit" name="login-form" class="btn btn-primary">Transmettre</button>
            </form>
        </div>
    </div>

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto">
            <!-- Display a link to propose to send a new password to email. -->
            <?php if (isset($errors['password']) && strlen($values['email']) > 0) { ?>
                <a href='<?php echo Routes::LOGIN . '?old-email=' . $values['email'] ?>'>Envoyer un nouveau mot de passe à l'adresse ci-dessus.</a>
            <?php } ?>
        </div>
    </div>
</div>