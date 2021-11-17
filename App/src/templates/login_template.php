<?php
################################
## Joël Piguet - 2021.11.17 ###
##############################


/**
 * rewrite previously entered email .
 * 
 * @param string $email User email previously entered into form.
 * @return string Escaped email value.
 */
function p_email($email): string
{
    #htmlentities is a php escape function to neutralize potentially harmful script.
    if ($email)
        return htmlentities($email);
    return '';
}

/**
 * Add valid/invalid class tag to the email input field.
 * 
 * @param string $errors Error array compiled in login_route.
 * @param string $email User email previously entered into form.
 * @return string Class tag or empty string.
 */
function email_input_validation($errors, $email): string
{
    if (isset($errors['email']))
        return ' is-invalid';
    if ($email)
        return ' is-valid';
    return '';
}

/**
 * Add invalid class tag to the password input field.
 * 
 * @param string $errors Error array compiled in login_route.
 * @return string Class tag or empty string.
 */
function password_input_validation($errors): string
{
    return isset($errors['password']) ? 'is-invalid' : '';
}
?>

<div class="container">
    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto mb-5">
            <form method="post" action="/login">

                <!-- display new password info alert -->
                <?php if (isset($alert['new-password'])) { ?>
                    <div class='alert alert-info alert-dismissible fade show' role='alert'>Un nouveau mot de passe a été envoyé à '<?php echo $email ?>'
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                <?php } ?>
                <!-- user logout info alert -->
                <?php if (isset($alert['disconnect'])) { ?>
                    <div class='alert alert-info alert-dismissible fade show' role='alert'>L'usager précédent s'est déconnecté.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>"
                <?php } ?>

                <label class="h4 m-4">Formulaire d'identification</label>
                <div class="mb-3">
                    <label for="form-email" class="form-label">Adresse e-mail</label>
                    <input id="form-email" type="email" name="email" aria-describedby="id-descr" class="form-control <?php echo email_input_validation($form_errors, $email) ?>" value=<?php echo p_email($email) ?>>

                    <!-- Print regular or error message under the email input field. -->
                    <?php if (isset($form_errors['email'])) { ?>
                        <div class='invalid-feedback'><?php echo $form_errors['email'] ?> </div>
                    <?php } else if (!$email) { ?>
                        <div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>
                    <?php } ?>

                </div>
                <div class="mt-3 mb-3">
                    <label for="form-password" class="form-label">Mot de passe</label>
                    <input id="form-password" type="password" name="password" class="form-control <?php echo password_input_validation($form_errors) ?>">

                    <!-- Print error message under the password input field in case of error. -->
                    <?php if (isset($form_errors['password'])) { ?>
                        <div class='invalid-feedback'><?php echo $form_errors['password'] ?> </div>
                    <?php } ?>

                </div>
                <button type="submit" name="login-form" class="btn btn-primary">Transmettre</button>
            </form>
        </div>
    </div>

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto">
            <!-- Display a link to propose to send a new password to email. -->
            <?php if (isset($form_errors['password']) && strlen($email) > 0) { ?>
                <a href='/login?old-email=<?php echo $email ?>'>Envoyer un nouveau mot de passe à l'adresse ci-dessus.</a>
            <?php } ?>
        </div>
    </div>
</div>