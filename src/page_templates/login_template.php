<?php
################################
## Joël Piguet - 2021.12.14 ###
##############################

use app\constants\Requests;

?>

<div class="container">

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto mb-5">
            <form method="post" action="/login">

                <label class="h4 m-4">Formulaire d'identification</label>
                <div class="mb-3">
                    <label for="form-email" class="form-label">Adresse e-mail</label>
                    <input id="form-email" type="email" name='login-email' aria-describedby="id-descr" value="<?php echo htmlentities($login_email); ?>" class="form-control 
                        <?php echo isset($warnings['login-email']) ? ' is-invalid' : '' ?>
                        <?php echo $login_email ? ' is-valid' : '' ?>">
                    <?php if (isset($warnings['login-email'])) { ?>
                        <div class='invalid-feedback'><?php echo $warnings['login-email'] ?> </div>
                    <?php } else { ?>
                        <div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>
                    <?php } ?>

                </div>
                <div class="mt-3 mb-3">
                    <label for="form-password" class="form-label">Mot de passe</label>
                    <input id="form-password" type="password" name='password' class="form-control 
                        <?php echo isset($warnings['password']) ? ' is-invalid' : '' ?>
                        <?php echo $password ? ' is-valid' : '' ?>">
                    <?php if (isset($warnings['password'])) { ?>
                        <div class='invalid-feedback'><?php echo $warnings['password'] ?> </div>
                    <?php } ?>

                </div>
                <button type="submit" name="login-form" class="btn btn-primary">Transmettre</button>
            </form>
        </div>
    </div>

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto">
            <!-- Display a link to propose to send a new password to email. -->
            <?php if (isset($warnings['password']) && strlen($login_email) > 0) { ?>
                <a href='<?php echo Requests::RENEW_PASSWORD . $login_email ?>'>Envoyer un nouveau mot de passe à l'adresse ci-dessus.</a>
            <?php } ?>
        </div>
    </div>
</div>