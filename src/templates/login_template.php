<?php
################################
## Joël Piguet - 2021.12.01 ###
##############################

use app\helpers\TUtil;

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

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto mb-5">
            <form method="post" action="/login">

                <label class="h4 m-4">Formulaire d'identification</label>
                <div class="mb-3">
                    <label for="form-email" class="form-label">Adresse e-mail</label>
                    <input id="form-email" type="email" name='email' aria-describedby="id-descr" value="<?php echo TUtil::escape($email); ?>" class="form-control 
                        <?php echo isset($errors['email']) ? ' is-invalid' : '' ?>
                        <?php echo $email ? ' is-valid' : '' ?>">
                    <?php if (isset($errors['email'])) { ?>
                        <div class='invalid-feedback'><?php echo $errors['email'] ?> </div>
                    <?php } else { ?>
                        <div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>
                    <?php } ?>

                </div>
                <div class="mt-3 mb-3">
                    <label for="form-password" class="form-label">Mot de passe</label>
                    <input id="form-password" type="password" name='password' class="form-control 
                        <?php echo isset($errors['password']) ? ' is-invalid' : '' ?>
                        <?php echo $password ? ' is-valid' : '' ?>">
                    <?php if (isset($errors['password'])) { ?>
                        <div class='invalid-feedback'><?php echo $errors['password'] ?> </div>
                    <?php } ?>

                </div>
                <button type="submit" name="login-form" class="btn btn-primary">Transmettre</button>
            </form>
        </div>
    </div>

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto">
            <!-- Display a link to propose to send a new password to email. -->
            <?php if (isset($errors['password']) && strlen($email) > 0) { ?>
                <a href='<?php echo LOGIN . '?old-email=' . $email ?>'>Envoyer un nouveau mot de passe à l'adresse ci-dessus.</a>
            <?php } ?>
        </div>
    </div>
</div>