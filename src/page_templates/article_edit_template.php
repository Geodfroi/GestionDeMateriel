<?php
################################
## Joël Piguet - 2022.01.28 ###
##############################

use app\constants\Route;

?>

<div class="container mt-4">

    <form>
        <div class="row">
            <label id='form-label' class="h4 text-center">Ajouter un article</article></label>
        </div>

        <div for="article-name" class="form-label col-md-8 mx-auto">Nom de l'article:</div>

        <div class="mb-2 col-md-8 mx-auto">
            <input id="article-name" name="article-name" type="text" class="form-control">
            <div id="article-name-feedback" class='invalid-feedback'></div>
        </div>

        <div for="location" class="form-label col-md-8 mx-auto">Emplacement:</div>

        <div class="row mx-auto mb-1 d-md-none">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Emplacements prédéfinis</button>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php foreach ($loc_presets as $item) { ?>
                    <li><span class="dropdown-item loc-preset"><?php echo $item->getContent() ?></span></li>
                <?php } ?>
            </ul>
        </div>

        <div class="col-md-8 mx-auto mb-1">
            <div class="input-group">
                <input id="location" name="location" type="text" class="form-control">

                <button class="btn btn-outline-secondary dropdown-toggle d-none d-md-block" type="button" data-bs-toggle="dropdown" aria-expanded="false">Emplacements prédéfinis</button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php foreach ($loc_presets as $item) { ?>
                        <li><span class="dropdown-item loc-preset"><?php echo $item->getContent() ?></span></li>
                    <?php } ?>
                </ul>
                <div id="location-feedback" class='invalid-feedback'></div>
            </div>
        </div>

        <div for="expiration-date" class="form-label col-md-8 mx-auto">Date de péremption:</div>

        <div class="mb-2 col-md-8 mx-auto">
            <input id="expiration-date" name="expiration-date" type="date" placeholder=<?php echo date('d/m/Y'); ?> class="form-control">
            <div id="expiration-date-feedback" class='invalid-feedback'></div>
        </div>

        <div class="mb-2 col-md-8 mx-auto">
            <textarea id="comments" name="comments" rows="4" placeholder="Vos commentaires." aria-describedby="id-comments" class="form-control"></textarea>
            <div id="id-comments" class="form-text">Vos commentaires vous seront rappelés dans le message d'alerte.</div>
            <div id="comments-feedback" class='invalid-feedback'></div>
        </div>

        <div class="col-8 mx-auto d-none d-md-flex justify-content-end">
            <a href="<?php echo Route::ART_TABLE ?>" class="btn btn-secondary col-2 ms-1">Annuler</a>
            <button type="submit" class="submit-btn btn btn-primary col-2">Ajouter</button>
        </div>

        <div class="row mx-auto mb-1 d-md-none mb-4">
            <button type="submit" class="submit-btn btn btn-primary mb-1">Ajouter</button>
            <a href=" <?php echo Route::ART_TABLE ?>" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>