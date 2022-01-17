<?php
################################
## Joël Piguet - 2022.01.17 ###
##############################

use app\constants\Requests;

?>

<div class="container">

    <div class="row-12 justify-content-center">
        <div class="col-6 mx-auto mb-5">
            <form>
                <label class="h4 m-4">Formulaire d'identification</label>
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <input id="email" type="email" name='login-email' aria-describedby="id-descr" class="form-control ">
                    <div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>
                    <div id="email-feedback" class='invalid-feedback'><?php echo $warnings['login-email'] ?> </div>
                </div>
                <div class="mt-3 mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" type="password" name='password' class="form-control ">
                    <div id="password-feedback" class='invalid-feedback'> </div>
                </div>
                <button id="submit-btn" type="submit" class="btn btn-primary">Transmettre</button>
            </form>
        </div>
    </div>

    <div id="rewew-div" hidden class="row-12 justify-content-center">
        <div class="col-6 mx-auto">
            <!-- Display a link to propose to send a new password to email. -->
            <a id="renew-link" href-start="<?php echo Requests::FORGOTTEN_PASSWORD ?>" href="">Envoyer un nouveau mot de passe à </a>
        </div>
    </div>
</div>