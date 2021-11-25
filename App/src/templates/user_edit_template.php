<?php
################################
## Joël Piguet - 2021.11.25 ###
##############################

use routes\Routes;
use helpers\TemplateUtil;

?>

<div class="container">
    <div class="row col-8">
        <form method="post" action="<?php echo USER_EDIT ?>">
            <label class="h4 m-4">Ajouter un utilisateur.</article></label>

            <input type="hidden" name="id" value="<?php echo $values['id'] ?>">

            <div class="mb-2">
                <label for="form-email" class="form-label col-3">E-mail de l'utilisateur:</label>
                <input id="form-email" name="email" type="email" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'email') ?>" value="<?php echo TemplateUtil::escape($values['email']) ?>">

                <?php if (isset($errors['email'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['email'] ?> </div>
                <?php } ?>
            </div>

            <div class="input-group mb-2">
                <label for="form-password" class="form-label col-12">Password:</label>
                <input id="form-password" name="password" type="text" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'password') ?>" value="<?php echo TemplateUtil::escape($values['password']) ?>">

                <a href="" class="btn btn-secondary">Regénérer</a>

                <?php if (isset($errors['password'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['password'] ?> </div>
                <?php } ?>
            </div>

            <button type="submit" name="<?php echo $values['id'] === 'no-id' ? 'new-user' : 'update-user' ?>" class="btn btn-primary">
                <?php if ($values['id'] === 'no-id') { ?>
                    Ajouter
                <?php } else { ?>
                    Modifier
                <?php } ?>
            </button>
            <a href="<?php echo ADMIN ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</div>

<div class="div">TODO: layout for small screen</div>