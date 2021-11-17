<?php
################################
## Joël Piguet - 2021.11.17 ###
##############################

use helpers\DateFormatter;
use routes\Routes;

?>

<div class="container mt-3">
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
    <div class="row">
        <a href="<?php echo Routes::ARTICLE_EDIT ?>" class="btn btn-primary">Ajouter un article</a>

    </div>
</div>