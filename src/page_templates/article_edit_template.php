<?php
################################
## Joël Piguet - 2022.01.17 ###
##############################

use app\constants\Route;
use app\helpers\Database;

$loc_presets = Database::locations()->queryAll();

?>

<div class="container">
    <div class="row col-8">
        <form>
            <label id='form-label' class="h4 m-4">Ajouter un article</article></label>
            <input id="id" type="hidden" name="id" value="<?php echo $id ?>">
            <div class="mb-2">
                <label for="article-name" class="form-label col-3">Nom de l'article:</label>
                <input id="article-name" name="article-name" type="text" class="form-control">
                <div id="article-name-feedback" class='invalid-feedback'></div>
            </div>

            <div class="mb-2">
                <label for="location" class="form-label col-3">Emplacement:</label>

                <div class="input-group">
                    <input id="location" name="location" type="text" class="form-control">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Emplacements prédéfinis</button>
                    <ul class="dropdown-menu dropdown-menu-end">

                        <?php foreach ($loc_presets as $item) { ?>
                            <li id="loc-preset-" <?php echo $item->getId() ?>><span class="dropdown-item loc-preset"><?php echo $item->getContent() ?></span></li>
                        <?php } ?>

                    </ul>
                    <div id="location-feedback" class='invalid-feedback'></div>
                </div>
            </div>

            <div class=" mb-2">
                <label for="expiration-date" class="form-label col-3">Date de péremption:</label>
                <input id="expiration-date" name="expiration-date" type="date" placeholder=<?php echo date('d/m/Y'); ?> class="form-control">
                <div id="expiration-date-feedback" class='invalid-feedback'></div>
            </div>

            <div class=" mb-2">
                <textarea id="comments" name="comments" rows="4" placeholder="Vos commentaires." aria-describedby="id-comments" class="form-control">
                </textarea>
                <div id="id-comments" class="form-text">Vos commentaires vous seront rappelés dans le message d'alerte.</div>
                <div id="comments-feedback" class='invalid-feedback'></div>
            </div>

            <button id="submit-btn" type="submit" class="btn btn-primary">Ajouter</button>
            <a href="<?php echo Route::ART_TABLE ?>" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>