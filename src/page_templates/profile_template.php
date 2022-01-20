<?php
################################
## Joël Piguet - 2022.01.20 ###
##############################

?>

<div class="container">
    <div class="row justify-content-center mx-auto mt-4 mb-2">
        <span class="text-center text-info border border-primary border-2 rounded py-2 col-12 col-md-8"> <?php echo "Compte utilisateur : $login_email" ?></span>
    </div>

    <div class="row justify-content-center mx-auto  mb-3" data-bs-toggle="tooltip" title="Par défaut, l'email est utilisé pour identifier l'utilisateur au sein de l'application. Un nom d'usager peut être défini de façon facultative." data-bs-placement="bottom">
        <a class="btn btn-outline-primary col-12 col-md-8 mt-4" data-bs-toggle="modal" data-bs-target="#alias-modal">Définir un nom d'usager.</a>
    </div>

    <div class="row justify-content-center mx-auto"> <a class="btn btn-outline-primary mb-3 col-12 col-md-8" data-bs-toggle="modal" data-bs-target="#password-modal">Changer de mot de passe</a></div>

    <div class="row justify-content-center mx-auto mb-3 " data-bs-toggle="tooltip" title="Il n'est pas possible de changer l'adresse de login, mais une adresse de contact supplémentaire peut être ajoutée." data-bs-placement="bottom">
        <a class=" btn btn-outline-primary col-12 col-md-8 " data-bs-toggle="modal" data-bs-target="#contact-modal">Ajouter une adresse de contact</a>
    </div>

    <div class="row justify-content-center mx-auto mb-3  " data-bs-toggle="tooltip" title="Par défault, l'application envoie un e-mail de rappel une première fois 2 semaines  avant qu'un article arrive à péremption, puis une nouvelle fois trois jours avant." data-bs-placement="bottom">
        <a class="btn btn-outline-primary col-12 col-md-8" data-bs-toggle="modal" data-bs-target="#delay-modal">Modifier le délai de contact</a>
    </div>
</div>


<!-- Modal window for new alias -->
<div id="alias-modal" class="modal fade" id="alias-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title">Définir un nom d'usager.</h5>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input id="alias" name="alias" type="text" class="form-control">
                        <div id="alias-feedback" class="invalid-feedback"></div>
                        <div class="form-text">Définissez un alias au sein de l'application. Il est plus facile pour autrui d'identifier un utilisateur par un alias plutôt que par un e-mail.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button id="alias-submit" type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal window for new password -->
<div class="modal fade" id="password-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title">Changer de mot de passe.</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input id="password" name="password" type="password" class="form-control">
                        <div id="password-feedback" class="invalid-feedback"></div>
                        <div class="form-text">Entrez un nouveau mot de passe.</div>
                    </div>

                    <div class="mb-3">
                        <input id="password-repeat" name="password-repeat" type="password" class="form-control">
                        <div id="password-repeat-feedback" class="invalid-feedback"></div>
                        <div class="form-text">Entrez le nouveau mot de passe une seconde fois.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button id="password-submit" type="submit" name="new-password" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal window for new contact email -->
<div class="modal fade" id="contact-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une adresse de contact.</h5>
                </div>
                <div class="modal-body">
                    <input id="contact-email" class=col-11 name="contact-email" type="email" class="form-control">&nbsp;&nbsp;</span><i class="bi bi-info-circle" role="img" style="font-size: 1.0rem;" data-bs-toggle="tooltip" title="Il faut prendre garde à correctement taper l'adresse car l'application ne vérifie pas que cette dernière soit fonctionnelle." data-bs-placement="right"></i>
                    <div id="contact-email-feedback" class="invalid-feedback"> </div>
                    <div class="form-text">Spécifier l'e-mail de contact auquel les courriers de rappels sont envoyés.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button id="contact-submit" type="submit" name="set-email" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal window for new contact email -->
<div class="modal fade" id="delay-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le délai de contact.</h5>
                </div>
                <div class="modal-body">
                    <label class="h-6 mb-4">Définir la date de l'envoi des e-mails de rappel de la date de péremption du produit. Il est possible de cocher plusieurs options, auquel cas plusieurs e-mails seront envoyés dans les délais spécifiés</label>

                    <div class="form-check mb-3">
                        <input id="delay-3" class="form-check-input" type="checkbox" name="delay-3" value="">
                        <label class="form-check-label" for="delay-3">
                            Envoyer un e-mail de rappel trois jours avant la péremption du produit.
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input id="delay-7" class="form-check-input" type="checkbox" value="" name="delay-7">
                        <label class="form-check-label" for="delay-7">
                            Envoyer un e-mail de rappel une semaine avant la péremption du produit.
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input id="delay-14" class="form-check-input" type="checkbox" value="" name="delay-14">
                        <label class="form-check-label" for="delay-14">
                            Envoyer un e-mail de rappel deux semaine avant la péremption du produit.
                        </label>
                    </div>
                    <div class="form-check">
                        <input id="delay-30" class="form-check-input" type="checkbox" value="" name="delay-30">
                        <label class="form-check-label" for="delay-30">
                            Envoyer un e-mail de rappel un mois avant la péremption du produit.
                        </label>

                        <div id="delay-feedback" class="invalid-feedback"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button id="delay-submit" type="submit" name="set-delay" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>