<?php
################################
## Joël Piguet - 2021.11.25 ###
##############################

use helpers\TUtil;

$is_admin = $values['is-admin'];

?>

<div class="container">
    <div class="row col-8">
        <form method="post" action="<?php echo USER_EDIT ?>">
            <label class="h4 m-4">Ajouter un utilisateur.</article></label>

            <div class="mb-2">
                <label for="form-email" class="form-label col-12">E-mail de l'utilisateur:</label>
                <input id="form-email" name="email" type="email" class="form-control <?php echo TUtil::showValid($errors, $values, 'email') ?>" value="<?php echo TUtil::escape($values['email']) ?>">

                <?php if (isset($errors['email'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['email'] ?> </div>
                <?php } ?>
            </div>

            <div class="input-group mb-3">
                <label for="form-password" class="form-label col-12">Password:</label>
                <input id="form-password" name="password" type="text" class="form-control <?php echo TUtil::showValid($errors, $values, 'password') ?>" value="<?php echo TUtil::escape($values['password']) ?>">

                <button type="submit" name="regen-password" class="btn btn-secondary">Regénérer</button>

                <?php if (isset($errors['password'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['password'] ?> </div>
                <?php } ?>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" name="is_admin" id="flexSwitchCheckDefault" <?php echo $is_admin ? 'checked' : '' ?>>
                <label class="form-check-label" for="flexSwitchCheckDefault>">Accorder les privilèges administratif à cet utilisateur.</label>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-modal">Ajouter</button>
            <a href="<?php echo ADMIN ?>" class="btn btn-secondary">Annuler</a>

            <!-- Modal window for user creation confirmation -->
            <div class="modal fade" id="create-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="create-modalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="create-modalLabel"><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
                        </div>
                        <div class="modal-body">
                            A l'ajout de l'utilisateur, un e-mail lui est automatiquement envoyé pour lui confirmer son inscription.</div>
                        <div class="modal-footer">
                            <button type="submit" name="new-user" class="btn btn-primary">Confirmer</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<div class="div">TODO: layout for small screen.</div>