<?php
################################
## Joël Piguet - 2021.11.14 ###
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
 * @return string Class tag.
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
 * print regular or error message under the email input field.
 * 
 * @param string $errors Error array compiled in login_route.
 * @param string $email User email previously entered into form.
 * @return string HTML feedback content.
 */
function email_comment($errors, $email): string
{
    if (isset($errors['email']))
        return sprintf("<div class='invalid-feedback'>%s</div>", $errors['email']);
    if (!$email)
        return sprintf('<div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>');
    return '';
}

/**
 * Add invalid class tag to the password input field.
 * 
 * @param string $errors Error array compiled in login_route.
 * @return string Class tag.
 */
function password_input_validation($errors): string
{
    return isset($errors['password']) ? 'is-invalid' : '';
}

/**
 * Print error message under the password input field in case of error.
 * 
 * @param string $errors Error array compiled in login_route.
 * @return string HTML feedback content.
 */
function password_comment($errors): string
{
    return isset($errors['password']) ? sprintf("<div class='invalid-feedback'>%s</div>", $errors['password']) : '';
}
?>

<div class="d-flex justify-content-center">
    <form method="post" action="/login" class="w-50">

        <label class="h4 m-4">Formulaire de connexion</label>
        <div class="mb-3">
            <label for="form-email" class="form-label">Adresse e-mail</label>
            <input id="form-email" type="email" name="email" aria-describedby="id-descr" class="form-control <?php echo email_input_validation($form_errors, $email) ?>" value=<?php echo p_email($email) ?>>
            <?php echo email_comment($form_errors, $email) ?>

            <div class="mt-3 mb-3">
                <label for="form-password" class="form-label">Mot de passe</label>
                <input id="form-password" type="password" name="password" class="form-control <?php echo password_input_validation($form_errors) ?>">
                <?php
                //valid comments are not necessary for password, since a valid password will immediately trigger a change of page.
                echo password_comment($form_errors) ?>
            </div>
            <button type="submit" class="btn btn-primary">Transmettre</button>
    </form>
</div>