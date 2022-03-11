<?php
################################
## Joël Piguet - 2022.01.13 ###
##############################

use app\constants\Route;

?>

<div class="container">
    <div class="row col-8">
        <form method="post" action="<?php echo Route::USER_EDIT ?>">
            <label class="h4 m-4">Ajouter un utilisateur.</article></label>

            <div class="mb-2">
                <label for="login-email" class="form-label col-12">E-mail de l'utilisateur:</label>
                <input id="login-email" name="login-email" type="email" value="<?php echo htmlentities($login_email) ?>" class="form-control 
                    <?php echo isset($warnings['login-email']) ? ' is-invalid' : '' ?>
                    <?php echo $email ? ' is-valid' : '' ?>">
                <?php if (isset($warnings['login-email'])) { ?>
                    <div class='invalid-feedback'><?php echo $warnings['login-email'] ?> </div>
                <?php } ?>
            </div>

            <div class="input-group mb-3">
                <label for="password" class="form-label col-12">Password:</label>
                <input id="password" name="password" type="text" value="<?php echo htmlentities($password) ?>" class="form-control 
                    <?php echo isset($warnings['password']) ? ' is-invalid' : '' ?>
                    <?php echo $password ? ' is-valid' : '' ?>">
                <button id="regen-password" name="regen-password" class="btn btn-secondary">Regénérer</button>

                <?php if (isset($warnings['password'])) { ?>
                    <div class='invalid-feedback'><?php echo $warnings['password'] ?> </div>
                <?php } ?>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" name="is-admin" id="flexSwitchCheckDefault" <?php echo $is_admin ? 'checked' : '' ?>>
                <label class="form-check-label" for="flexSwitchCheckDefault>">Accorder les privilèges administratif à cet utilisateur.</label>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-modal">Ajouter</button>
            <a href="<?php echo Route::ADMIN ?>" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>

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
                <button id="new-user-submit" type="submit" name="new-user" class="btn btn-primary">Confirmer</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>