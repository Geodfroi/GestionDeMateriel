<?php
################################
## Joël Piguet - 2021.12.01 ###
##############################

use helpers\ArtOrder;

$page = $_SESSION[ART_PAGE];

/**
 * Display caret icon besides table header to display order setting depending on _SESSION[ADMIN_ORDER_BY]
 * 
 * @param string $header Table header name
 * @return string Icon class name.
 */
function disCaretArt(string $header): string
{
    $orderby = $_SESSION[ART_ORDERBY];

    if ($header === 'article') {
        if ($orderby === ArtOrder::NAME_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === ArtOrder::NAME_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'location') {
        if ($orderby === ArtOrder::LOCATION_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === ArtOrder::LOCATION_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'per_date') {
        if ($orderby === ArtOrder::DATE_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === ArtOrder::DATE_DESC) {
            return 'bi-caret-down';
        }
    }
    return '';
}

/**
 * Compile header link depending on _SESSION[ADMIN_ORDER_BY]
 * 
 * @param string $header Table header name
 * @return string Header link to display in href.
 */
function disLinkArt(string $header): string
{
    $root = ART_TABLE . '?orderby=';
    $orderby = $_SESSION[ART_ORDERBY];
    error_log('$orderby: ' . $_SESSION[ART_ORDERBY]);

    // play with ASC / DESC to set default behavior the first time the column is clicked; ie per_date is listed most recent first.
    if ($header === 'article') {

        return $orderby === ArtOrder::NAME_ASC ? $root . ArtOrder::NAME_DESC : $root . ArtOrder::NAME_ASC;
    } else if ($header === 'location') {
        return $orderby === ArtOrder::LOCATION_ASC ? $root . ArtOrder::LOCATION_DESC : $root . ArtOrder::LOCATION_ASC;
    } else if ($header === 'per_date') {
        return $orderby === ArtOrder::DATE_DESC ? $root . ArtOrder::DATE_ASC : $root . ArtOrder::DATE_DESC;
    }
    return '';
}

?>

<div class="container mt-3">

    <div class="row">
        <div class="col-12">
            <?php if (isset($alert['type'])) { ?>
                <div class='text-center alert alert-<?php echo $alert['type'] ?> alert-dismissible fade show' role='alert'><?php echo $alert['msg'] ?>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="row col-12">
        <table class="table table-striped">

            <thead>
                <tr>
                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('article') ?>">Article <span class="<?php echo disCaretArt('article') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('location') ?>">Location <span class="<?php echo disCaretArt('location') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('per_date') ?>">Date de péremption <span class="<?php echo disCaretArt('per_date') ?>"></span></a>

                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($n = 0; $n < count($articles); $n++) {
                    $article = $articles[$n]; ?>
                    <tr>
                        <td><?php echo $article->getArticleName()  ?>
                            <?php if (strlen($article->getComments()) > 0) { ?>
                                <span class="bi-text-left text-info" data-bs-toggle="tooltip" title="<?php echo $article->getComments() ?>" data-bs-placement="right"></span>
                            <?php } ?>
                        </td>
                        <td><?php echo $article->getLocation() ?></td>
                        <td><?php echo $article->getExpirationDate()->format('d/m/Y') ?></td>
                        <td>
                            <a class="link-secondary" href=<?php echo ART_EDIT . '?update=' . $article->getId() ?>><i class="bi bi-pencil" role="img" style="font-size: 1.2rem;" aria-label=" update" data-bs-toggle="tooltip" title="Editer" data-bs-placement="bottom"></i></a>
                            <a class="link-danger ms-2" data-bs-toggle="modal" data-bs-target=<?php echo "#delete-modal-$n" ?>><i class="bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>
                        </td>

                        <!-- Modal window for article delete confirmation -->
                        <div class="modal fade" id=<?php echo "delete-modal-$n" ?> data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby=<?php echo "delete-modalLabel-$n" ?> aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id=<?php echo "delete-modalLabel-$n" ?>><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment supprimer [<?php echo $article->getArticleName() ?>] ?
                                    </div>
                                    <div class="modal-footer">
                                        <a href="<?php echo ART_TABLE . '?delete=' . $article->getId() ?>" class="btn btn-primary">Confirmer</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="list-pagination">
        <ul class="pagination justify-content-end">

            <li class="page-item <?php echo $page == 1 ? 'disabled' : '' ?>">
                <a href="<?php echo ART_TABLE . '?page=' . strval(intval($page) - 1) ?>" class="page-link" aria-label="Previous" <?php echo $page == 1 ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-left">
                    </span>
                </a>
            </li>

            <?php for ($n = 1; $n <= $page_count; $n++) {  ?>
                <li class=" page-item <?php echo $n == $page ? 'active' : '' ?>">
                    <a href="<?php echo ART_TABLE . '?page=' . $n ?>" class="page-link" <?php echo $n == $page ? 'tabindex = "-1"' : '' ?>><?php echo $n ?></a>
                </li>
            <?php  } ?>

            <li class="page-item  <?php echo $page == $page_count ? 'disabled' : '' ?>">
                <a href="<?php echo ART_TABLE . '?page=' .  strval(intval($page) + 1) ?>" class="page-link" aria-label="Next" <?php echo $page == $page_count ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-right"></span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="row">
        <a href="<?php echo ART_EDIT ?>" class="btn btn-primary">Ajouter une saisie</a>
    </div>
</div>

<div>TODO: created by column / date created</div>
<div>articles visible by all</div>
<div>TODO: filters</div>
<div>TODO: fixed column size</div>
<div>TODO: better adaptive layout</div>
<div>TODO: put tab logout link under email on the left</div>