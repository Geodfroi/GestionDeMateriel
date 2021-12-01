<?php
################################
## Joël Piguet - 2021.12.01 ###
##############################

use helpers\TUtil;

?>

<div class="container">
    <div class="row col-8">
        <form method="post" action="<?php echo ART_EDIT ?>">
            <label class="h4 m-4">Ajouter un article</article></label>

            <input type="hidden" name="id" value="<?php echo $id ?>">

            <div class="mb-2">
                <label for="form-name" class="form-label col-3">Nom de l'article:</label>
                <input id="form-name" name="article-name" type="text" value="<?php echo TUtil::escape($article_name) ?>" class="form-control
                    <?php echo isset($error['article-name']) ? ' is-invalid' : '' ?>
                    <?php echo $article_name ? ' is-valid' : '' ?>">
                <?php if (isset($errors['article-name'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['article-name'] ?> </div>
                <?php } ?>
            </div>

            <div class="mb-2">
                <label for="form-location" class="form-label col-3">Emplacement:</label>

                <div class="input-group">
                    <input id="form-location" name="location" type="text" value="<?php echo TUtil::escape($location) ?>" class="form-control
                        <?php echo isset($error['location']) ? ' is-invalid' : '' ?>
                        <?php echo $location ? ' is-valid' : '' ?>">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Emplacements prédéfinis</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li id="loc-preset-1"><span class="dropdown-item"><?php echo LOCATION_PRESET_1 ?></span></li>
                        <li id="loc-preset-2"><span class="dropdown-item"><?php echo LOCATION_PRESET_2 ?></span></li>
                        <li id="loc-preset-3"><span class="dropdown-item"><?php echo LOCATION_PRESET_3 ?></span></li>
                    </ul>
                </div>

                <?php if (isset($errors['location'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['location'] ?></div>
                <?php } ?>

            </div>

            <div class=" mb-2">
                <label for="form-expiration" class="form-label col-3">Date de péremption:</label>
                <input id="form-expiration" name="expiration-date" type="date" placeholder=<?php echo date('d/m/Y'); ?> value="<?php echo TUtil::escape($expiration_date) ?>" class="form-control 
                    <?php echo isset($error['expiration-date']) ? ' is-invalid' : '' ?>
                    <?php echo $expiration_date ? ' is-valid' : '' ?>">
                <?php if (isset($errors['expiration-date'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['expiration-date'] ?></div>
                <?php } ?>

            </div>

            <div class=" mb-2">
                <textarea id="form-comments" name="comments" rows="4" placeholder="Vos commentaires." aria-describedby="id-comments" class="form-control 
                    <?php echo isset($error['comments']) ? ' is-invalid' : '' ?>
                    <?php echo $comments ? ' is-valid' : '' ?>">
                </textarea>
                <div id="id-comments" class="form-text">Vos commentaires vous seront rappelés dans le message d'alerte.</div>
                <?php if (isset($errors['comments'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['comments'] ?></div>
                <?php } ?>
            </div>

            <button type="submit" name="<?php echo $id === 'no-id' ? 'new-article' : 'update-article' ?>" class="btn btn-primary">
                <?php if ($id === 'no-id') { ?>
                    Ajouter
                <?php } else { ?>
                    Modifier
                <?php } ?>
            </button>
            <a href="<?php echo ART_TABLE ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
    let loc_input = document.getElementById('form-location');
    document.getElementById('loc-preset-1').addEventListener('click', e => {
        loc_input.value = "<?php echo LOCATION_PRESET_1 ?>";
    })
    document.getElementById('loc-preset-2').addEventListener('click', e => {
        loc_input.value = "<?php echo LOCATION_PRESET_2 ?>";
    })
    document.getElementById('loc-preset-3').addEventListener('click', e => {
        loc_input.value = "<?php echo LOCATION_PRESET_3 ?>";
    })
</script>