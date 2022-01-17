<?php
################################
## Joël Piguet - 2022.01.18 ###
##############################

use app\constants\ArtFilter;
use app\constants\Requests;
use app\constants\Route;
use app\constants\Session;
use app\helpers\Database;
// use app\helpers\Logging;
use app\helpers\Util;
use app\models\Article;

$page = $_SESSION[Session::ART_PAGE];
$orderby = $_SESSION[Session::ART_ORDERBY];
$filters = $_SESSION[Session::ART_FILTERS];
// $filter_str = implode(';', $filters);

$filter_name = isset($filters[ArtFilter::NAME]) ? $filters[ArtFilter::NAME] : '';
$filter_location = isset($filters[ArtFilter::LOCATION]) ? $filters[ArtFilter::LOCATION] : '';

$filter_date_type = isset($filters[ArtFilter::DATE_BEFORE]) ? ArtFilter::DATE_BEFORE : ArtFilter::DATE_AFTER;
$filter_date_val = '';
if (isset($filters[ArtFilter::DATE_BEFORE])) {
    $filter_date_val = $filters[ArtFilter::DATE_BEFORE];
} else if (isset($filters[ArtFilter::DATE_AFTER])) {
    $filter_date_val = $filters[ArtFilter::DATE_AFTER];
}

$filter_show_expired = isset($filters[ArtFilter::SHOW_EXPIRED]);

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

<div id="filter-data" filter=<?php echo $filter_str ?>><?php echo $filter_str ?></div>

<div class="container mt-3">

    <div class="row">
        <a class="link-info text-decoration-none col-12 text-center fw-bold" href="" data-bs-toggle="modal" data-bs-target="#filter-modal" aria-label="filter">
            <i class="bi bi-filter" role="img" style="font-size: 1.5rem;"></i>
            <span id="filter-label">Filtres
            </span>
        </a>
    </div>

    <div class="row">
        <table id='table' class="table table-striped">

            <thead>
                <tr>
                    <th id="article-header"><a class="text-decoration-none" href="#">Article <span></span></a>
                    <th id="location-header"><a class="text-decoration-none" href="#">Location <span></span></a>
                    <th id="per-date-header"><a class="text-decoration-none" href="#">Délai de péremption <span></span></a>
                    <th id="owner-header"><a class="text-decoration-none" href="#">Créé par <span></span></a>
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
                            <a class="link-danger ms-2" data-bs-toggle="modal" data-bs-target="#delete-modal" data-bs-id="<?php echo $article->getId() ?>" data-bs-name="<?php echo $article->getArticleName() ?>"><i class="bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>
                        </td>
                        </tr>
                    <?php } ?>
            </tbody>
        </table>
    </div>

    <nav>
        <ul id="display-nav" display-count=<?php echo $display_count ?> class="nav justify-content-center">
            <li class="nav-item">
                <!-- text-secondary text-decoration-underline -->
                <a id="display-10" display-count="10" class="display-option nav-link px-0 active" href="<?php echo Route::ART_TABLE . '?display=10' ?>">10</a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-0 text-primary">|</a>
            </li>
            </li>
            <li class="nav-item">
                <a id="display-20" display-count="20" class="display-option nav-link px-0" href="<?php echo Route::ART_TABLE . '?display=20' ?>">20</a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-0 text-primary">|</a>
            </li>
            <li class="nav-item">
                <a id="display-50" display-count="50" class="display-option nav-link px-0" href="<?php echo Route::ART_TABLE . '?display=50' ?>">50</a>
            </li>
        </ul>

        <ul id="page-nav" page-current=<?php echo $page ?> page-count=<?php echo $page_count ?> class="pagination justify-content-end">

            <li id="page-last" class="page-item">
                <a href="#" class="page-link" aria-label="Previous" tabindex="-1">
                    <span aria-hidden="true" class="bi-chevron-double-left">
                    </span>
                </a>
            </li>

            <?php for ($n = 1; $n <= $page_count; $n++) {  ?>
                <li class="page-item <?php echo $n == $page ? 'active' : '' ?>">
                    <a href="<?php echo Route::ART_TABLE . '?page=' . $n ?>" class="page-link"><?php echo $n ?></a>
                </li>
            <?php  } ?>

            <li id="page-next" class="page-item">
                <a href="#" class="page-link" aria-label="Next" tabindex="-1">
                    <span aria-hidden="true" class="bi-chevron-double-right"></span>
                </a>
            </li>
        </ul>

    </nav>
    <div class="row">
        <a href="<?php echo Route::ART_EDIT ?>" class="btn btn-primary">Ajouter une saisie</a>
    </div>

</div>

<!-- Modal window for article delete confirmation -->
<div class="modal fade" id=<?php echo "delete-modal" ?> data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-modal-label"><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
            </div>
            <div class="modal-body">
                Voulez-vous vraiment supprimer l'article [] ?
            </div>
            <div class="modal-footer">
                <a href-start="<?php echo Requests::DELETE_ARTICLE ?>" href="" class="btn btn-primary">Confirmer</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filter-modal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="filter-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filter-modal-label">Filtrer les articles selon les paramètres suivants: </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?php echo Route::ART_TABLE ?>">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-1">
                                    <span class="input-group-text col-md-3">Par nom d'article:</span>
                                    <input id="filter-name" name="filter-name" class="form-control" type="text" aria-label="Filter-name" value="<?php echo htmlentities($filter_name) ?>">
                                </div>

                                <div class="input-group mb-1">
                                    <span class="input-group-text col-md-3">Par emplacement:</span>
                                    <input name="filter-location" class="form-control" type="text" aria-label="Filter-name" value="<?php echo htmlentities($filter_location) ?>">
                                </div>

                                <div class="input-group mb-2">
                                    <button id="filter-date-btn" class="btn btn-outline-secondary dropdown-toggle col-md-3" aria-expanded="false" data-bs-toggle="dropdown">
                                        <?php echo $filter_date_type === ArtFilter::DATE_BEFORE ? 'Péremption avant le' : 'Péremption après le'; ?>
                                    </button>
                                    <input id="filter-date-type" name="filter-date-type" type="hidden" value="<?php echo $filter_date_type; ?>">
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><span id="filter-date-before" class="dropdown-item filter-date-select">Péremption avant le</span></li>
                                        <li><span id="filter-date-after" class="dropdown-item filter-date-select">Péremption après le</span></li>
                                    </ul>
                                    <input id="filter-date-val" name="filter-date-val" class="form-control" type="date" value="<?php echo $filter_date_val; ?>">
                                    <button id="filter-date-clear" class="btn btn-outline-primary">Effacer</button>
                                </div>

                                <div class="form-check form-switch mb-1">
                                    <input id="show-expired" name="show-expired" class="form-check-input" type="checkbox" role="switch" <?php if ($filter_show_expired) { ?> checked <?php } ?>>
                                    <label class="form-check-label" for="show-expired">Montrer également les articles arrivés à péremption.</label>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" name="filter">Filtrer</button>
                </div>
            </form>
        </div>
    </div>
</div>