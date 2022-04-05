<?php
################################
## Joël Piguet - 2022.01.30 ###
##############################

use app\constants\Route;

?>

<div class="container mt-4">

    <form method="post" action="<?php echo Route::USER_EDIT ?>">

        <div class="row">
            <label id='form-label' class="h4 text-center">Ajouter un utilisateur</label>
        </div>

        <div class="row">
            <label for="login-email" class="form-label col-lg-8 mx-auto">E-mail de l'utilisateur:</label>
        </div>
        <div class="row mb-2">
            <div class="col-lg-8 mx-auto">
                <input id="login-email" name="login-email" type="email" class="form-control">
                <div id="login-email-feedback" class='invalid-feedback'></div>
            </div>
        </div>

        <div class="row">
            <label for="password" class="form-label col-lg-8 mx-auto">Password:</label>
        </div>
        <div class="row mb-3">
            <div class="col-lg-8 mx-auto">
                <div class="input-group">
                    <input id="password" name="password" type="text" class="form-control" value="<?php echo htmlentities($password) ?>">
                    <button id="regen-password" name="regen-password" class="btn btn-secondary">Regénérer</button>
                    <div id="password-feedback" class='invalid-feedback'></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="form-check form-switch mb-3">
                    <input id="is-admin" class="form-check-input" type="checkbox" role="switch" name="is-admin" id="flexSwitchCheckDefault">
                    <label class="form-check-label" for="flexSwitchCheckDefault>">Accorder les privilèges administratif à cet utilisateur.</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 mx-auto justify-content-end d-none d-lg-flex">
                <a class="btn btn-secondary col-2" href="<?php echo Route::ADMIN ?>">Annuler</a>
                <button class="add-btn btn btn-primary col-2 ms-1" type="submit">Ajouter</button>
            </div>
        </div>

        <div class="row mx-auto d-lg-none mb-1">
            <a class="btn btn-secondary" href="<?php echo Route::ADMIN ?>">Annuler</a>
        </div>
        <div class="row mx-auto d-lg-none mb-4">
            <button class="add-btn btn btn-primary" type="submit">Ajouter</button>
        </div>
    </form>
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

<div class="ms-3">DEBUG: créer nouveau utilisateur échoue car la fonctionalité email n'est pas installée.</div>