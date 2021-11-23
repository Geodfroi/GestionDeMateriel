<?php
################################
## Joël Piguet - 2021.11.23 ###
##############################

use routes\Login;
use routes\Routes;

?>

<div class="container mt-3">
    <div class="row col-12">

        <?php if (isset($alerts['added-alert'])) { ?>
            <?php if ($alerts['added-alert'] == 'added_success') { ?>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>L'article a été correctement enregistré.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            <?php } ?>

            <?php if ($alerts['added-alert'] == 'added_failure') { ?>
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>Erreur: l'article n'a pas pu être ajouté à la liste. Veuillez réessayer.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            <?php } ?>

        <?php } ?>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>Article</th>
                    <th>Location</th>
                    <th>Date de péremption</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article) { ?>
                    <tr>
                        <td><?php echo $article->getArticleName() ?></td>
                        <td><?php echo $article->getLocation() ?></td>
                        <td><?php echo $article->getExpirationDate()->format('d/m/Y') ?></td>
                        <td>
                            <a href="">Modifier</a>
                            <a href=<?php Routes::ARTICLES . '?/delete=' . $article->getId() ?>>Effacer</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <a href="<?php echo Routes::ARTICLE_EDIT ?>" class="btn btn-primary">Ajouter une saisie</a>
    </div>
</div>