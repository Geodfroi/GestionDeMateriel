<?php
################################
## Joël Piguet - 2021.12.07 ###
##############################

use app\helpers\Database;
use app\models\Article;

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
        if ($orderby === NAME_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === NAME_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'location') {
        if ($orderby === LOCATION_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === LOCATION_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'per_date') {
        if ($orderby === DATE_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === DATE_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'owner') {
        if ($orderby === OWNED_BY) {
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

        return $orderby === NAME_ASC ? $root . NAME_DESC : $root . NAME_ASC;
    } else if ($header === 'location') {
        return $orderby === LOCATION_ASC ? $root . LOCATION_DESC : $root . LOCATION_ASC;
    } else if ($header === 'per_date') {
        return $orderby === DATE_DESC ? $root . DATE_ASC : $root . DATE_DESC;
    } else if ($header === 'owner') {
        return $root . OWNED_BY;
    }
    return '';
}

/**
 * @return string Article owner display alias.
 */
function getOwner(Article $article): string
{
    $user = Database::users()->queryById($article->getUserId());
    if ($user) {
        return sprintf("%s (%s)", $user->getDisplayAlias(), $article->getCreationDate()->format('d.m.Y'));
    }
    return "Inconnu";
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

    <div class="row">
        <form method="post" action="<?php echo ART_TABLE ?>">
            <div class="input-group">
                <input name="filter-type" type="hidden">
                <button id="filter-btn" class="btn btn-outline-secondary dropdown-toggle" aria-expanded="false" data-bs-toggle="dropdown"></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span id="0" class="dropdown-item filter-item">Par nom d'article</span></li>
                    <li><span id="1" class="dropdown-item filter-item">Par emplacement</span></li>
                    <li><span id="2" class="dropdown-item filter-item">Par date de péremption</span></li>
                </ul>

                <input name="filter-txt" class="form-control" type="search" placeholder="filtre" aria-label="Filter">
                <button class="btn btn-primary" type="submit" name="filter">Filtrer</button>
        </form>
    </div>

    <div class="row">

        <table class="table table-striped">

            <thead>
                <tr>
                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('article') ?>">Article <span class="<?php echo disCaretArt('article') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('location') ?>">Location <span class="<?php echo disCaretArt('location') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('per_date') ?>">Date de péremption <span class="<?php echo disCaretArt('per_date') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('owner') ?>">Créé par <span class="<?php echo disCaretArt('owner') ?>"></span></a>

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
                        <td><?php echo getOwner($article) ?></td>

                        <td>
                            <a class="link-secondary" href=<?php echo ART_EDIT . '?update=' . $article->getId() ?>><i class="bi bi-pencil" role="img" style="font-size: 1.2rem;" aria-label=" update" data-bs-toggle="tooltip" title="Modifier" data-bs-placement="bottom"></i></a>
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

    <nav>
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

<script>
    let btn = document.getElementById('filter-btn');
    let hidden_input = document.getElementById('filter-type');

    let collection = document.getElementsByClassName('filter-item');
    for (let index = 0; index < collection.length; index++) {
        const element = collection[index];
        element.addEventListener('click', e => {
            btn.innerText = element.innerText;
            hidden_input.value = parseInt(element.id);
        });
    }
</script>


<div>TODO: color scheme for dates / peremption</div>
<div>TODO: filters</div>
<div>TODO: fixed column size</div>
<div>TODO: better adaptive layout</div>