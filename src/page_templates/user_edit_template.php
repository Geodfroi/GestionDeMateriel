<?php
################################
## Joël Piguet - 2022.01.16 ###
##############################

use app\constants\Route;

?>

<div class="container">
    <div class="row col-8">
        <form method="post" action="<?php echo Route::USER_EDIT ?>">
            <label class="h4 m-4">Ajouter un utilisateur.</article></label>

            <div class="mb-2">
                <label for="login-email" class="form-label col-12">E-mail de l'utilisateur:</label>
                <input id="login-email" name="login-email" type="email" class="form-control">
                <div id="login-email-feedback" class='invalid-feedback'> </div>
            </div>

            <div class="input-group mb-3">
                <label for="password" class="form-label col-12">Password:</label>
                <input id="password" name="password" type="text" class="form-control" value="<?php echo htmlentities($password) ?>">
                <button id="regen-password" name="regen-password" class="btn btn-secondary">Regénérer</button>
                <div id="password-feedback" class='invalid-feedback'> </div>
            </div>

            <div class="form-check form-switch mb-3">
                <input id="is-admin" class="form-check-input" type="checkbox" role="switch" name="is-admin" id="flexSwitchCheckDefault">
                <label class="form-check-label" for="flexSwitchCheckDefault>">Accorder les privilèges administratif à cet utilisateur.</label>
            </div>

            <button id="add-btn" type="button" class="btn btn-primary">Ajouter</button>
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
                <button id="submit-btn" type="submit" name="new-user" class="btn btn-primary">Confirmer</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>