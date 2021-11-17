<?php
################################
## Joël Piguet - 2021.11.17 ###
##############################

?>

<div class="row col-12">
    <form method="post" action="/articles">
        <label class="h4 m-4">Ajouter un article</article></label>
        <div class="mb-2">
            <label for="form-name" class="form-label col-2">Nom de l'article:</label>
            <input id="form-name" name="article-name" type="text col-12" class="form-control">
        </div>
        <div class="mb-2">
            <label for="form-location" class="form-label col-2">Emplacement:</label>
            <input id="form-location" name="location" type="text col-12" class="form-control">
        </div>
        <div class=" mb-2">
            <label for="form-expiration" class="form-label col-2">Date de péremption:</label>
            <input id="form-expiration" name="expiration-date" type="text" placeholder=<?php echo date('d/m/Y'); ?> class="form-control">
        </div>
        <div class=" mb-2">
            <textarea id="form-comments" name="comments" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" name="new-article" class="btn btn-primary">Ajouter</button>
    </form>
</div>