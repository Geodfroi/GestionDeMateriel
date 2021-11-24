<?php
################################
## Joël Piguet - 2021.11.24 ###
##############################

use routes\Routes;
use helpers\TemplateUtil;

?>
<div class="container">
    <div class="row col-8">
        <form method="post" action="<?php echo Routes::ART_EDIT ?>">
            <label class="h4 m-4">Ajouter un article</article></label>

            <input type="hidden" name="id" value="<?php echo $values['id'] ?>">

            <div class="mb-2">
                <label for="form-name" class="form-label col-3">Nom de l'article:</label>
                <input id="form-name" name="article-name" type="text col-12" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'article-name') ?>" value="<?php echo TemplateUtil::escape($values['article-name']) ?>">

                <?php if (isset($errors['article-name'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['article-name'] ?> </div>
                <?php } ?>

            </div>

            <div class="mb-2">
                <label for="form-location" class="form-label col-3">Emplacement:</label>
                <input id="form-location" name="location" type="text col-12" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'location') ?>" value="<?php echo TemplateUtil::escape($values['location']) ?>">

                <?php if (isset($errors['location'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['location'] ?></div>
                <?php } ?>

            </div>

            <div class=" mb-2">
                <label for="form-expiration" class="form-label col-3">Date de péremption:</label>
                <input id="form-expiration" name="expiration-date" type="date" placeholder=<?php echo date('d/m/Y'); ?> class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'expiration-date') ?>" value="<?php echo TemplateUtil::escape($values['expiration-date']) ?>">

                <?php if (isset($errors['expiration-date'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['expiration-date'] ?></div>
                <?php } ?>

            </div>


            <div class=" mb-2">
                <textarea id="form-comments" name="comments" class="form-control <?php echo TemplateUtil::setValidity($errors, $values, 'comments') ?>" rows="4" placeholder="Vos commentaires." aria-describedby="id-comments"><?php echo TemplateUtil::escape($values['comments']) ?></textarea>
                <div id="id-comments" class="form-text">Vos commentaires vous seront rappelés dans le message d'alerte.</div>

                <?php if (isset($errors['comments'])) { ?>
                    <div class='invalid-feedback'><?php echo $errors['comments'] ?></div>
                <?php } ?>
            </div>

            <button type="submit" name="<?php echo $values['id'] === 'no-id' ? 'new-article' : 'update-article' ?>" class="btn btn-primary">
                <?php if ($values['id'] === 'no-id') { ?>
                    Ajouter
                <?php } else { ?>
                    Modifier
                <?php } ?>
            </button>
            <a href="/" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</div>