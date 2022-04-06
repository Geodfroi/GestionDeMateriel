<?php
################################
## Joël Piguet - 2021.12.09 ###
##############################

use app\constants\Route;

?>

<div class="container">

    <!-- Profile change list -->
    <?php if ($display == 0) { ?>

        <div class="row">
            <span class="text-center text-info border border-primary border-2 rounded mt-4 mb-2 py-2 col-12 col-md-6 mx-auto"> <?php echo "Compte utilisateur : $login_email" ?></span>
        </div>

        <div class="row" data-bs-toggle="tooltip" title="Par défaut, l'email est utilisé pour identifier l'utilisateur au sein de l'application. Un nom d'usager peut être défini de façon facultative." data-bs-placement="bottom"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto mt-4" href="<?php echo Route::PROFILE . '?set_alias=1' ?>">Définir un nom d'usager.</a></div>

        <div class="row"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo Route::PROFILE . '?change_password=1' ?>">Changer de mot de passe</a></div>

        <div class="row" data-bs-toggle="tooltip" title="Il n'est pas possible de changer l'adresse de login, mais une adresse de contact supplémentaire peut être ajoutée." data-bs-placement="bottom"> <a class=" btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo Route::PROFILE . '?add_email=1' ?>">Ajouter une adresse de contact</a></div>

        <div class="row" data-bs-toggle="tooltip" title="Par défault, l'application envoie un e-mail de rappel une première fois 2 semaines  avant qu'un article arrive à péremption, puis une nouvelle fois trois jours avant." data-bs-placement="bottom">
            <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo Route::PROFILE . '?modify_delay=1' ?>">Modifier le délai de contact</a>
        </div>
    <?php } else { ?>

        <?php if ($display == 1) { ?>
            <!-- Set user alias -->
            <form method="post" action=<?php echo Route::PROFILE ?>>
                <div class="row mt-4 mb-3">
                    <div class="col-12 col-md-8 mx-auto">
                        <input name="alias" type="text" id="alias" class="form-control <?php echo isset($warnings['alias']) ? ' is-invalid' : '' ?>" value=<?php echo htmlentities($alias) ?>>

                        <?php if (isset($warnings['alias'])) { ?>
                            <div class='invalid-feedback'><?php echo $warnings['alias'] ?> </div>
                        <?php } else { ?>
                            <div id="id-descr" class="form-text">Définissez un alias au sein de l'application. Il est plus facile pour autrui d'identifier un utilisateur par un alias plutôt que par un e-mail.</div>
                        <?php } ?>
                    </div>
                </div>

                <div class="row">
                    <button type="submit" name="set-alias" class="btn btn-primary col-12 col-md-6 mx-auto mb-2">Enregistrer</button>
                </div>
            </form>
        <?php } elseif ($display == 2) { ?>
            <!-- Modify password form -->
            <form method="post" action=<?php echo Route::PROFILE ?>>
                <div class="row mt-4 mb-3">
                    <div class="col-12 col-md-8 mx-auto">
                        <input name="password" type="password" id="password" value="<?php echo htmlentities($password) ?>" class="form-control
                            <?php echo isset($warnings['password']) ? ' is-invalid' : '' ?>
                            <?php echo $password ? ' is-valid' : '' ?>">
                        <?php if (isset($warnings['password'])) { ?>
                            <div class='invalid-feedback'><?php echo $warnings['password'] ?> </div>
                        <?php } else { ?>
                            <div class="form-text">Entrer un nouveau mot de passe.</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-8 mx-auto">
                        <input name="password-repeat" type="password" id="password-repeat" value="<?php echo htmlentities($password_repeat) ?>" class="form-control 
                            <?php echo isset($warnings['password-repeat']) ? ' is-invalid' : '' ?>
                            <?php echo $password ? ' is-valid' : '' ?>">
                        <?php if (isset($warnings['password-repeat'])) { ?>
                            <div class='invalid-feedback'><?php echo $warnings['password-repeat'] ?> </div>
                        <?php } else { ?>
                            <div class="form-text">Entrer le nouveau mot de passe une seconde fois.</div>
                        <?php } ?>
                    </div>
                </div>

                <div class="row">
                    <button type="submit" name="new-password" class="btn btn-primary col-12 col-md-6 mx-auto mb-2">Enregistrer</button>
                </div>
            </form>

        <?php } elseif ($display == 3) { ?>
            <!-- Add contact email form -->

            <div class="row mt-4 mb-3">
                <div class="col-12 col-md-8 mx-auto">
                    <label for="login-input" class="form-label">L'e-mail d'inscription. <i class="bi bi-info-circle" role="img" style="font-size: 1.0rem;" data-bs-toggle="tooltip" title="L'e-mail d'inscription sert d'identifiant dans la base de donnée et ne peut ainsi pas être modifié." data-bs-placement="right"></i></label>
                    <input id="login-input" class="form-control" type="text" value="<?php echo $login_email ?>" disabled readonly>
                </div>
            </div>

            <form method="post" action="<?php echo Route::PROFILE ?>">
                <div class="row mb-4">
                    <div class="col-12 col-md-8 mx-auto">

                        <label for="contact-input" class="form-label">L'e-mail de contact pour recevoir les courriers de rappels. <i class="bi bi-info-circle" role="img" style="font-size: 1.0rem;" data-bs-toggle="tooltip" title="Il faut prendre garde à correctement taper l'adresse car l'application ne vérifie pas que cette dernière soit fonctionnelle." data-bs-placement="right"></i></label>
                        <input id="contact-input" name="contact-email" type="email" value="<?php echo htmlentities($contact_email) ?>" class="form-control
                            <?php echo isset($warnings['contact-email']) ? ' is-invalid' : '' ?>
                            <?php echo $password ? ' is-valid' : '' ?>">
                        <?php if (isset($warnings['contact-email'])) { ?>
                            <div class='invalid-feedback'><?php echo $warnings['contact-email'] ?> </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <button type="submit" name="set-email" class="btn btn-primary col-12 col-md-6 mx-auto mb-2">Enregistrer</button>
                </div>
            </form>

        <?php } elseif ($display == 4) { ?>
            <!-- Set delay before contact email form -->

            <form method="post" action="<?php echo Route::PROFILE ?>">
                <div class="row mb-4 mt-4">
                    <div class="col-12 col-md-8 mx-auto">
                        <label class="h-6 mb-4">Définir la date de l'envoi des e-mails de rappel de la date de péremption du produit. Il est possible de cocher plusieurs options, auquel cas plusieurs e-mails seront envoyés dans les délais spécifiés</label>

                        <div class="form-check mb-3">
                            <input class="form-check-input  <?php echo isset($warnings['delays']) ? ' is-invalid' : '' ?>" type="checkbox" name="delay-3" value="" id="delay-3" <?php echo in_array(3, $delays) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="delay-3">
                                Envoyer un e-mail de rappel trois jours avant la péremption du produit.
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input  <?php echo isset($warnings['delays']) ? ' is-invalid' : '' ?>" type="checkbox" value="" name="delay-7" id="delay-7" <?php echo in_array(7, $delays) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="delay-7">
                                Envoyer un e-mail de rappel une semaine avant la péremption du produit.
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input  <?php echo isset($warnings['delays']) ? ' is-invalid' : '' ?>" type="checkbox" value="" name="delay-14" id="delay-14" <?php echo in_array(14, $delays) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="delay-14">
                                Envoyer un e-mail de rappel deux semaine avant la péremption du produit.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input  <?php echo isset($warnings['delays']) ? ' is-invalid' : '' ?>" type="checkbox" value="" name="delay-30" id="delay-30" <?php echo in_array(30, $delays) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="delay-30">
                                Envoyer un e-mail de rappel un mois avant la péremption du produit.
                            </label>
                            <?php if (isset($warnings['delays'])) { ?>
                                <div class='invalid-feedback'><?php echo $warnings['delays'] ?> </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <button type="submit" name="set-delay" class="btn btn-primary col-12 col-md-6 mx-auto mb-2">Enregistrer</button>
                </div>
            </form>
        <?php } ?>

        <div class="row"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo Route::PROFILE ?>">Revenir</a></div>
    <?php }  ?>
</div>