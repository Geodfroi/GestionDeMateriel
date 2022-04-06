<?php
################################
## Joël Piguet - 2022.03.14 ###
##############################

use app\constants\Route;

?>

<div class="container mt-4">

    <form>
        <div class="row">
            <label id='form-label' class="h4 text-center">Ajouter un article</label>
        </div>

        <div class="row">
            <div for="article-name" class="form-label col-lg-8 mx-auto">Nom de l'article:</div>
        </div>

        <div class="row">
            <div class="mb-2 col-lg-8 mx-auto">
                <input id="article-name" name="article-name" type="text" class="form-control">
                <div id="article-name-feedback" class='invalid-feedback'></div>
            </div>
        </div>

        <div class="row">
            <div for="location" class="form-label col-lg-8 mx-auto">Emplacement:</div>
        </div>

        <div class="row d-lg-none mb-1 mx-auto">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Emplacements prédéfinis</button>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php foreach ($loc_presets as $item) { ?>
                    <li><span class="dropdown-item loc-preset"><?php echo $item->getContent() ?></span></li>
                <?php } ?>
            </ul>
        </div>


        <div class="row mb-1">
            <div class="col-lg-8 mx-auto">
                <div class="input-group">
                    <input id="location" name="location" type="text" class="form-control">
                    <button class="btn btn-outline-secondary dropdown-toggle d-none d-lg-block" type="button" data-bs-toggle="dropdown" aria-expanded="false">Emplacements prédéfinis</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach ($loc_presets as $item) { ?>
                            <li><span class="dropdown-item loc-preset"><?php echo $item->getContent() ?></span></li>
                        <?php } ?>
                    </ul>
                    <div id="location-feedback" class='invalid-feedback'></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div for="expiration-date" class="form-label col-lg-8 mx-auto">Date de péremption:</div>
        </div>

        <div class="row">
            <div class="mb-2 col-lg-8 mx-auto">
                <input id="expiration-date" name="expiration-date" type="date" placeholder=<?php echo date('d/m/Y'); ?> class="form-control">
                <div id="expiration-date-feedback" class='invalid-feedback'></div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-8 mx-auto">
                <textarea id="comments" name="comments" rows="4" placeholder="Vos commentaires." aria-describedby="id-comments" class="form-control"></textarea>
                <div id="id-comments" class="form-text">Vos commentaires vous seront rappelés dans le message d'alerte.</div>
                <div id="comments-feedback" class='invalid-feedback'></div>
            </div>
        </div>

        <div class="row">
            <div class="col-8 mx-auto d-none d-lg-flex justify-content-end">
                <a href="<?php echo Route::ART_TABLE ?>" class="btn btn-secondary col-2">Annuler</a>
                <button type="submit" class="submit-btn btn btn-primary col-2 ms-1">Ajouter</button>
            </div>
        </div>

        <div class="row mx-auto mb-1 d-lg-none mb-1">
            <a href="<?php echo Route::ART_TABLE ?>" class="btn btn-secondary">Annuler</a>
        </div>
        <div class="row mx-auto mb-1 d-lg-none mb-4">
            <button type="submit" class="submit-btn btn btn-primary">Ajouter</button>
        </div>
    </form>
</div>

<script>
    let article = <?php echo $article ? json_encode($article, JSON_UNESCAPED_UNICODE) : "false" ?>;
    console.dir(article);
    let mode = "<?php echo $mode ?>";
    console.log(mode);
</script>