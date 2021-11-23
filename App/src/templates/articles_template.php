<?php
################################
## Joël Piguet - 2021.11.23 ###
##############################

use routes\Routes;
use helpers\ArticleOrder;

?>

<div class="container mt-3">

    <div class="row col-12">
        <?php if (isset($alerts['success'])) { ?>
            <div class='alert alert-success alert-dismissible fade show' role='alert'><?php echo $alerts['success'] ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php } ?>

        <?php if (isset($alerts['failure'])) { ?>
            <div class='alert alert-warning alert-dismissible fade show' role='alert'><?php echo $alerts['failure'] ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php } ?>

        <table class="table table-striped">

            <thead>
                <tr>
                    <?php
                    // Display clickable ordering icons (caret-down, caret-up and circle) besides header labels.
                    ?>
                    <th>Article

                        <?php if ($orderby ===  ArticleOrder::ORDER_BY_NAME_DESC) { ?><a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_NAME_ASC ?> "><i class="bi-caret-down" style="font-size: 1.2rem;"></i><a>
                                <?php } else if ($orderby ===  ArticleOrder::ORDER_BY_NAME_ASC) { ?>
                                    <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_NAME_DESC ?> "><i class="bi-caret-up" style="font-size: 1.2rem"></i><a>
                                        <?php } else { ?>
                                            <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_NAME_ASC ?> "><i class="bi-circle" style="font-size: 1.0rem;"></ <?php } ?> </th>
                    <th>Location <?php if ($orderby ===  ArticleOrder::ORDER_BY_LOCATION_DESC) { ?>
                            <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_LOCATION_ASC ?> "><i class="bi-caret-down" style="font-size: 1.2rem;"></i><a>
                                <?php } else if ($orderby ===  ArticleOrder::ORDER_BY_LOCATION_ASC) { ?>
                                    <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_LOCATION_DESC ?> "><i class="bi-caret-up" style="font-size: 1.2rem"></i><a>
                                        <?php } else { ?>
                                            <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_LOCATION_ASC ?> "><i class="bi-circle" style="font-size: 1.0rem;"></ <?php } ?> </th>
                    </th>
                    <th>Date de péremption <?php if ($orderby ===  ArticleOrder::ORDER_BY_DATE_DESC) { ?>
                            <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_DATE_ASC ?> "><i class="bi-caret-down" style="font-size: 1.2rem;"></i><a>
                                <?php } else if ($orderby ===  ArticleOrder::ORDER_BY_DATE_ASC) { ?>
                                    <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_DATE_DESC ?> "><i class="bi-caret-up" style="font-size: 1.2rem"></i><a>
                                        <?php } else { ?>
                                            <a href="<?php echo Routes::ARTICLES . '?orderby=' . ArticleOrder::ORDER_BY_DATE_DESC ?> "><i class="bi-circle" style="font-size: 1.0rem;"></ <?php } ?></th>
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
                            <a class="link-secondary" href=<?php echo Routes::ARTICLE_EDIT . '?update=' . $article->getId() ?>><i class="bi-pencil" role="img" style="font-size: 1.2rem;" aria-label=" update"></i></a>
                            <span style=" font-size: 1.2rem;">|</span>
                            <a class="link-danger" href=<?php echo Routes::ARTICLES . '?delete=' . $article->getId() ?>><i class="bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete"></i></a>
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