<?php
################################
## Joël Piguet - 2021.11.16 ###
##############################

use helpers\DateFormatter;

?>

<div class="container">
    <h4>Articles enregistrés</h4>
    <div class="row col-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Location</th>
                    <th>Date de péremption</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article) { ?>
                    <tr>
                        <td><?php echo $article->getArticleName() ?></td>
                        <td><?php echo $article->getLocation() ?></td>
                        <td><?php echo DateFormatter::printDateFrenchFormat($article->getExpirationDate()) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="row col-12">
        <form method="post" action="/articles">
            <label class="h4 m-4">Ajouter un article</article></label>
            <div class="mb-2">
                <label for="form-name" class="form-label col-2">Nom de l'article:</label>
                <input id="form-name" name="article-name" type="text col-12">
            </div>
            <div class="mb-2">
                <label for="form-location" class="form-label col-2">Emplacement:</label>
                <input id="form-location" name="location" type="text col-12">
            </div>
            <div class="mb-2">
                <label for="form-expiration" class="form-label col-2">Date de péremption:</label>
                <input id="form-expiration" name="expiration-date" type="text" placeholder=<?php echo date('d/m/Y'); ?>>
            </div>

            <button type="submit" name="new-article" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</div>