<?php
################################
## Joël Piguet - 2022.01.19 ###
##############################

use app\constants\Requests;

?>

<div class="container">

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto mb-5">
            <form>
                <label class="h4 m-4">Formulaire d'identification</label>
                <div class="mb-3">
                    <label for="login" class="form-label">Adresse e-mail ou alias</label>
                    <input id="login" type="text" name='login' aria-describedby="id-descr" class="form-control">
                    <div id="id-descr" class="form-text">Entrer votre adresse e-mail ou alias pour vous identifier.</div>
                    <div id="login-feedback" class='invalid-feedback'><?php echo $warnings['login'] ?> </div>
                </div>
                <div class="mt-3 mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <input id="password" type="password" name='password' class="form-control">
                        <button id="show-password-btn" class="btn btn-outline-primary"><i class="bi bi-eye"></i></button>
                        <div id="password-feedback" class='invalid-feedback'></div>
                    </div>
                </div>
                <button id="submit-btn" type="submit" class="btn btn-primary">Transmettre</button>
            </form>
        </div>
    </div>

    <div id="rewew-div" class="row-12 justify-content-center" hidden>
        <div class="col-6 mx-auto">
            <!-- Display a link to propose to send a new password to email. -->
            <a id="renew-link" href-start="<?php echo Requests::FORGOTTEN_PASSWORD ?>" href="">Envoyer un nouveau mot de passe à </a>
        </div>
    </div>
</div>