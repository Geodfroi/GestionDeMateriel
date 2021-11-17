<?php
################################
## Joël Piguet - 2021.11.17 ###
##############################

use routes\Routes;
use helpers\TemplateUtil;
use routes\ArtEdit;


?>
<div class="container">
    <div class="row col-8">
        <form method="post" action="<?php echo Routes::ARTICLE_EDIT ?>">
            <label class="h4 m-4">Ajouter un article</article></label>
            <div class="mb-2">
                <label for="form-name" class="form-label col-2">Nom de l'article:</label>
                <input id="form-name" name="<?php echo ArtEdit::ARTICLE_KEY ?>" type="text col-12" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, ArtEdit::ARTICLE_KEY) ?>" value="<?php echo TemplateUtil::escape($article_name) ?>">

                <div class='invalid-feedback'><?php echo $errors[ArtEdit::ARTICLE_KEY] ?> </div>

            </div>
            <div class="mb-2">
                <label for="form-location" class="form-label col-2">Emplacement:</label>
                <input id="form-location" name="<?php echo ArtEdit::LOCATION_KEY ?> " type="text col-12" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, ArtEdit::LOCATION_KEY) ?>" value="<?php echo TemplateUtil::escape($values[ArtEdit::LOCATION_KEY]) ?>">

                <div class='invalid-feedback'><?php echo $errors[ArtEdit::LOCATION_KEY] ?></div>

            </div>
            <div class=" mb-2">
                <label for="form-expiration" class="form-label col-2">Date de péremption:</label>
                <input id="form-expiration" name="<?php echo ArtEdit::DATE_EXP_KEY ?>" type="text" placeholder=<?php echo date('d/m/Y'); ?> class="form-control <?php echo TemplateUtil::setValidity($errors, $values, ArtEdit::DATE_EXP_KEY) ?>" value="<?php echo TemplateUtil::escape($values[ArtEdit::DATE_EXP_KEY]) ?>">
            </div>
            <div class=" mb-2">
                <textarea id="form-comments" name="comments" class="form-control" rows="4"></textarea>
            </div>

            <button type="submit" name="new-article" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</div>