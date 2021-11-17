<?php
################################
## Joël Piguet - 2021.11.17 ###
##############################

use helpers\TemplateUtil;
use routes\Login;
use routes\Routes;

?>

<div class="container">
    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto mb-5">
            <form method="post" action="/login">

                <!-- display new password info alert -->
                <?php if (isset($alerts[LOGIN::NEW_PASSWORD_ALERT])) { ?>
                    <div class='alert alert-info alert-dismissible fade show' role='alert'>Un nouveau mot de passe a été envoyé à '<?php echo $values[Login::EMAIL_KEY] ?>'
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                <?php } ?>
                <!-- user logout info alert -->
                <?php if (isset($alerts[LOGIN::DISCONNECT_ALERT])) { ?>
                    <div class='alert alert-info alert-dismissible fade show' role='alert'>L'usager précédent s'est déconnecté.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>"
                <?php } ?>

                <label class="h4 m-4">Formulaire d'identification</label>
                <div class="mb-3">
                    <label for="form-email" class="form-label">Adresse e-mail</label>
                    <input id="form-email" type="email" name=<?php echo Login::EMAIL_KEY ?> aria-describedby="id-descr" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, Login::EMAIL_KEY) ?>
                        " value=<?php echo TemplateUtil::escape($values[Login::EMAIL_KEY]); ?>>

                    <div class='invalid-feedback'><?php echo $errors[Login::EMAIL_KEY] ?> </div>

                    <?php if (!isset($errors[Login::EMAIL_KEY])) { ?>
                        <div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>
                    <?php } ?>

                </div>
                <div class="mt-3 mb-3">
                    <label for="form-password" class="form-label">Mot de passe</label>
                    <input id="form-password" type="password" name=<?php echo Login::PASSWORD_KEY ?> class="form-control <?php echo TemplateUtil::setValidity($errors, $values, Login::PASSWORD_KEY) ?>">

                    <div class='invalid-feedback'><?php echo $errors[Login::PASSWORD_KEY] ?> </div>

                </div>
                <button type="submit" name="login-form" class="btn btn-primary">Transmettre</button>
            </form>
        </div>
    </div>

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto">
            <!-- Display a link to propose to send a new password to email. -->
            <?php if (isset($errors[Login::PASSWORD_KEY]) && strlen($values[Login::EMAIL_KEY]) > 0) { ?>
                <a href='<?php echo Routes::LOGIN . '?' . Login::GET_OLD_EMAIL . '=' . $values[Login::EMAIL_KEY] ?>'>Envoyer un nouveau mot de passe à l'adresse ci-dessus.</a>
            <?php } ?>
        </div>
    </div>
</div>