<?php
################################
## Joël Piguet - 2021.12.09 ###
##############################

use app\constants\Filter;
use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\helpers\Database;
use app\helpers\Util;
use app\models\Article;


$page = $_SESSION[Session::ART_PAGE];
$filter_type = intval($_SESSION[Session::ART_FILTER_TYPE]);
$filter_val = $_SESSION[Session::ART_FILTER_VAL];

/**
 * Display caret icon besides table header to display order setting depending on _SESSION[ADMIN_ORDER_BY]
 * 
 * @param string $header Table header name
 * @return string Icon class name.
 */
function disCaretArt(string $header): string
{
    $orderby = $_SESSION[Session::ART_ORDERBY];

    if ($header === 'article') {
        if ($orderby === OrderBy::NAME_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === OrderBy::NAME_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'location') {
        if ($orderby === OrderBy::LOCATION_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === OrderBy::LOCATION_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'per_date') {
        if ($orderby === OrderBy::DELAY_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === OrderBy::DELAY_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'owner') {
        if ($orderby === OrderBy::OWNED_BY_ASC) {
            return 'bi-caret-up';
        } else if ($orderby === OrderBy::OWNED_BY_DESC) {
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
    $root = Route::ART_TABLE . '?orderby=';
    $orderby = $_SESSION[Session::ART_ORDERBY];

    // play with ASC / DESC to set default behavior the first time the column is clicked; ie per_date is listed most recent first.
    if ($header === 'article') {
        return $orderby === OrderBy::NAME_ASC ? $root . OrderBy::NAME_DESC : $root . OrderBy::NAME_ASC;
    } else if ($header === 'location') {
        return $orderby === OrderBy::LOCATION_ASC ? $root . OrderBy::LOCATION_DESC : $root . OrderBy::LOCATION_ASC;
    } else if ($header === 'per_date') {
        return $orderby === OrderBy::DELAY_DESC ? $root . OrderBy::DELAY_ASC : $root . OrderBy::DELAY_DESC;
    } else if ($header === 'owner') {
        return $orderby === OrderBy::OWNED_BY_DESC ? $root . OrderBy::OWNED_BY_ASC : $root . OrderBy::OWNED_BY_DESC;
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

        //take only caracters before @ if it is an email.
        $alias  = explode('@', $user->getAlias())[0];
        return sprintf("%s (%s)", $alias, $article->getCreationDate()->format('d.m.Y'));
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
        <div class="col-12 col-md-8 mx-auto">
            <form method="post" action="<?php echo Route::ART_TABLE ?>">
                <div class="input-group">
                    <input id="filter-type" name="filter-type" type="hidden" value="<?php echo $filter_type ?>">
                    <button id="filter-btn" class="btn btn-outline-secondary dropdown-toggle" aria-expanded="false" data-bs-toggle="dropdown">
                        <?php echo Filter::getLabel($filter_type); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span id="<?php echo Filter::ARTICLE_NAME ?>" class="dropdown-item filter-item">
                                <?php echo Filter::getLabel(FILTER::ARTICLE_NAME) ?></span></li>
                        <li><span id="<?php echo Filter::LOCATION ?>" class="dropdown-item filter-item">
                                <?php echo Filter::getLabel(FILTER::LOCATION) ?></span></li>
                        <li><span id="<?php echo Filter::DATE_BEFORE ?>" class="dropdown-item filter-item">
                                <?php echo Filter::getLabel(FILTER::DATE_BEFORE) ?></span></li>
                        <li><span id="<?php echo Filter::DATE_AFTER ?>" class="dropdown-item filter-item">
                                <?php echo Filter::getLabel(FILTER::DATE_AFTER) ?></span></li>
                    </ul>

                    <input id="filter-val" name="filter-val" class="form-control" type="date" placeholder="filtre" aria-label="Filter" value="<?php echo $filter_val ?>">

                    <button class="btn btn-primary" type="submit" name="filter">Filtrer</button>
            </form>
        </div>
    </div>

    <div class="row">

        <table class="table table-striped">

            <thead>
                <tr>
                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('article') ?>">Article <span class="<?php echo disCaretArt('article') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('location') ?>">Location <span class="<?php echo disCaretArt('location') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('per_date') ?>">Délai de péremption <span class="<?php echo disCaretArt('per_date') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('owner') ?>">Créé par <span class="<?php echo disCaretArt('owner') ?>"></span></a>

                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($n = 0; $n < count($articles); $n++) {
                    $article = $articles[$n];
                    $day_until = Util::getDaysUntil($article->getExpirationDate()); ?>

                    <?php if ($day_until <= 0) { ?>
                        <tr class="bg-secondary">
                        <?php } else { ?>
                        <tr>
                        <?php } ?>

                        <!-- Article -->
                        <td><?php echo $article->getArticleName()  ?>
                            <?php if (strlen($article->getComments()) > 0) { ?>
                                <span class="bi-text-left text-info" data-bs-toggle="tooltip" title="<?php echo $article->getComments() ?>" data-bs-placement="right"></span>
                            <?php } ?>
                        </td>
                        <td><?php echo $article->getLocation() ?></td>

                        <!-- Délai de péremption -->
                        <td>
                            <?php echo $article->getExpirationDate()->format('d/m/Y') ?>
                            <span>&emsp;</span>
                            <?php if ($day_until <= 0) { ?>
                                <span><?php echo 'échu' ?></span>
                            <?php } elseif ($day_until <= 3) { ?>
                                <span class="bg-dark text-danger mark"><?php echo sprintf('%s jour(s)', $day_until) ?>&ensp;<i class="bi bi-exclamation-triangle-fill"></i></span>
                            <?php } elseif ($day_until <= 7) { ?>
                                <span class="bg-dark text-warning mark"><?php echo sprintf('%s jour(s)', $day_until) ?>&ensp;<i class="bi bi-exclamation-circle-fill"></i></span>
                            <?php } else { ?>
                                <span><?php echo sprintf('%s jour(s)', $day_until) ?></span>
                            <?php } ?>

                        </td>

                        <!-- Créé par -->
                        <td><?php echo getOwner($article) ?></td>
                        <!-- Actions -->
                        <td>
                            <a class="link-success" href=<?php echo Route::ART_EDIT . '?update=' . $article->getId() ?>><i class="bi bi-pencil" role="img" style="font-size: 1.2rem;" aria-label=" update" data-bs-toggle="tooltip" title="Modifier" data-bs-placement="bottom"></i></a>
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
                                        <a href="<?php echo Route::ART_TABLE . '?delete=' . $article->getId() ?>" class="btn btn-primary">Confirmer</a>
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
                <a href="<?php echo Route::ART_TABLE . '?page=' . strval(intval($page) - 1) ?>" class="page-link" aria-label="Previous" <?php echo $page == 1 ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-left">
                    </span>
                </a>
            </li>

            <?php for ($n = 1; $n <= $page_count; $n++) {  ?>
                <li class=" page-item <?php echo $n == $page ? 'active' : '' ?>">
                    <a href="<?php echo Route::ART_TABLE . '?page=' . $n ?>" class="page-link" <?php echo $n == $page ? 'tabindex = "-1"' : '' ?>><?php echo $n ?></a>
                </li>
            <?php  } ?>

            <li class="page-item  <?php echo $page == $page_count ? 'disabled' : '' ?>">
                <a href="<?php echo Route::ART_TABLE . '?page=' .  strval(intval($page) + 1) ?>" class="page-link" aria-label="Next" <?php echo $page == $page_count ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-right"></span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="row">
        <a href="<?php echo Route::ART_EDIT ?>" class="btn btn-primary">Ajouter une saisie</a>
    </div>
</div>

<script>
    // set btn filter inner text and fill filter-type input value field.
    let btn = document.getElementById('filter-btn');
    let filter_type_input = document.getElementById('filter-type');
    let filter_val_input = document.getElementById('filter-val');

    //change input type depending on filter-type.
    function changeInputFieldType() {
        //Warning: use loose equality here -> compare str and int values.
        if (filter_type_input.value == <?php echo Filter::ARTICLE_NAME ?> || filter_type_input.value == <?php echo Filter::LOCATION ?>) {
            filter_val_input.type = 'search';
        } else {
            filter_val_input.type = 'date';
        }
    }

    changeInputFieldType();

    let collection = document.getElementsByClassName('filter-item');
    for (let index = 0; index < collection.length; index++) {
        const element = collection[index];
        element.addEventListener('click', e => {
            btn.innerText = element.innerText;
            filter_type_input.value = parseInt(element.id);
            changeInputFieldType();
            filter_val_input.value = '';
        });
    }
</script>