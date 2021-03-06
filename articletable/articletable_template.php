<?php
################################
## Joël Piguet - 2022.04.06 ###
##############################

use app\constants\ArtFilter;
use app\constants\Route;
use app\helpers\Util;

?>

<div class="container mt-3">

    <div class="row justify-content-center mx-auto">
        <a class="btn btn-primary col-12 d-lg-none" href="<?php echo Route::ART_EDIT ?>">Ajouter une saisie</a>
    </div>

    <div class="row mx-auto">
        <a class="link-info text-decoration-none col-12 text-center fw-bold" href="" data-bs-toggle="modal" data-bs-target="#filter-modal" aria-label="filter">
            <i class="bi bi-filter" role="img" style="font-size: 1.5rem;"></i>
            <span id="filter-label">Filtres
            </span>
        </a>
    </div>

    <div class="row mx-auto">
        <table id='table' class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th id="article-header">
                        <a class="text-decoration-none" href="#">Article
                            <span class="d-none d-lg-inline">Article</span>
                            <span class="d-inline d-lg-none">Art. </span>
                            <span class="icon"></span>
                        </a>
                    </th>
                    <th id="location-header">
                        <a class="text-decoration-none" href="#">
                            <span class="d-none d-lg-inline">Location</span>
                            <span class="d-inline d-lg-none">Loc. </span>
                            <span class="icon"></span>
                        </a>
                    </th>
                    <th id="per-date-header">
                        <a class="text-decoration-none" href="#">
                            <span class="d-none d-lg-inline">Délai de péremption </span>
                            <span class="d-inline d-lg-none">Date pér. </span>
                            <span class="icon"></span>
                        </a>
                    </th>
                    <th class="d-none d-lg-table-cell" id="owner-header"><a class="text-decoration-none" href="#">Créé par <span class="icon"></span></a>
                    </th>
                    <th class="d-none d-lg-table-cell">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($n = 0; $n < count($articles); $n++) {
                    $article = $articles[$n];
                    $day_until = Util::getDaysUntil($article->getExpirationDate()); ?>

                    <?php if ($day_until <= 0) { ?>
                        <tr class="table-row bg-secondary" data-bs-id="<?php echo $article->getId() ?>" data-bs-name="<?php echo $article->getArticleName() ?>">
                        <?php } else { ?>
                        <tr class="table-row" data-bs-id="<?php echo $article->getId() ?>" data-bs-name="<?php echo $article->getArticleName() ?>">
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
                        <td class="d-none d-lg-table-cell"><?php echo $article->getOwner() ?></td>

                        <!-- Actions -->
                        <td class="d-none d-lg-table-cell">
                            <a id="update-link" class="link-success" href=<?php echo Route::ART_EDIT . '?update=' . $article->getId() ?>><i class="bi bi-pencil" role="img" style="font-size: 1.2rem;" aria-label=" update" data-bs-toggle="tooltip" title="Modifier" data-bs-placement="bottom"></i></a>

                            <a id="delete-link" class="link-danger ms-2" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>
                        </td>
                        </tr>
                    <?php } ?>
            </tbody>
        </table>
    </div>

    <nav>
        <div class="row mx-auto">
            <div class="col-0 col-lg-5"></div>
            <ul id="display-nav" class="nav justify-content-lg-center col-6 col-lg-2">
                <li class="nav-item">
                    <!-- text-secondary text-decoration-underline -->
                    <a id="display-10" display-count="10" class="display-option nav-link px-0 active" href="<?php echo Route::ART_TABLE . '?display_count=10' ?>">10</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 text-primary">|</a>
                </li>
                </li>
                <li class="nav-item">
                    <a id="display-20" display-count="20" class="display-option nav-link px-0" href="<?php echo Route::ART_TABLE . '?display_count=20' ?>">20</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-0 text-primary">|</a>
                </li>
                <li class="nav-item">
                    <a id="display-50" display-count="50" class="display-option nav-link px-0" href="<?php echo Route::ART_TABLE . '?display_count=50' ?>">50</a>
                </li>
            </ul>

            <ul id="page-nav" class="pagination justify-content-end col-6 col-lg-2 ms-auto">

                <li id="page-last" class="page-item">
                    <a href="#" class="page-link" aria-label="Previous" tabindex="-1">
                        <span aria-hidden="true" class="bi-chevron-double-left">
                        </span>
                    </a>
                </li>

                <?php for ($n = 1; $n <= $display_data['page_count']; $n++) {  ?>
                    <li class="page-item <?php echo $n == $display_data['page'] ? 'active' : '' ?>">
                        <a href="<?php echo Route::ART_TABLE . '?page=' . $n ?>" class="page-link"><?php echo $n ?></a>
                    </li>
                <?php  } ?>

                <li id="page-next" class="page-item">
                    <a href="#" class="page-link" aria-label="Next" tabindex="-1">
                        <span aria-hidden="true" class="bi-chevron-double-right"></span>
                    </a>
                </li>
            </ul>
        </div>

    </nav>

    <div class="row justify-content-center mx-auto">
        <a class="btn btn-primary col-6 d-none d-lg-block" href="<?php echo Route::ART_EDIT ?>">Ajouter une saisie</a>
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
                <a href="#" class="btn btn-primary">Confirmer</a>
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
                <h5 class="modal-title d-none d-lg-block" id="filter-modal-label">Filtrer les articles selon les paramètres suivants: </h5>
                <h3 class="modal-title d-lg-none" id="filter-modal-label">Filtres</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="<?php echo Route::ART_TABLE ?>">
                <div class="modal-body">
                    <div class="mb-2">
                        <div class="input-group">
                            <span class="d-none d-lg-block input-group-text col-lg-3">Par nom d'article:</span>
                            <input class="form-control" id="filter-name" name="filter-name" type="text" aria-label="Filter-name">
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="input-group mb-2">
                            <span class="d-none d-lg-block input-group-text col-lg-3">Par emplacement:</span>
                            <input id="filter-location" name="filter-location" class="form-control" type="text" aria-label="Filter-location">
                            <button class="btn btn-outline-secondary dropdown-toggle col-12 col-lg-auto" type="button" data-bs-toggle="dropdown" aria-expanded="false">Prédéfinis</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php foreach ($loc_presets as $item) { ?>
                                    <li><span class="dropdown-item loc-preset"><?php echo $item->getContent() ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-2">
                        <input id="filter-author" name="filter-author" type="text" hidden>
                        <div class="input-group mb-2">
                            <span class="d-none d-lg-block input-group-text col-lg-3">Par créateur:</span>
                            <button id="filter-author-dropdown" class=" btn btn-outline-secondary dropdown-toggle form-control" type=" button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $display_data['filters'][ArtFilter::AUTHOR] ?></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item author-preset"><?php echo ArtFilter::EVERYONE ?></span></li>
                                <?php foreach ($authors as $author) { ?>
                                    <li><span class="dropdown-item author-preset"><?php echo $author->getAlias() ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-2">
                        <input id="filter-date-type" name="filter-date-type" type="hidden">
                        <div class="input-group">
                            <button class="filter-date-dropdown btn btn-outline-secondary dropdown-toggle col-12 col-lg-3" aria-expanded="false" data-bs-toggle="dropdown">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span filter-value="filter-date-before" class="dropdown-item filter-dropdown-item">Péremption avant le</span></li>
                                <li><span filter-value="filter-date-after" class="dropdown-item filter-dropdown-item">Péremption après le</span></li>
                            </ul>
                            <input id="filter-date-val" name="filter-date-val" class="form-control" type="date">
                            <button class="clear-filter btn btn-outline-primary">Effacer</button>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input id="filter-show-expired" name="filter-show-expired" class="form-check-input" type="checkbox" role="switch">
                        <label class="form-check-label" for="show-expired">Montrer également les articles arrivés à péremption.</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary col-12 col-lg-2" data-bs-dismiss="modal">Annuler</button>
                    <a class="btn btn-light col-12 col-lg-3" href="<?php echo Route::ART_TABLE . "?filter=clear-all" ?>" id="clear-filters-btn" type="button">Enlever tous les filtres</a>
                    <button type="submit" class="btn btn-primary col-12 col-lg-2" name="filter">Filtrer</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="action-modal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="filter-modal-label" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <span class="h5 mx-auto">Modifier []</span>
            </div>
            <div class="modal-body">
                <div class="row"><a id="update-btn" type="button" class="btn btn-primary">Mettre à jour</a></div>
                <div class="row mt-2"><a id="delete-btn" type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#delete-modal">Supprimer</a></div>
                <div class="row mt-2"><a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</a></div>
            </div>
        </div>
    </div>
</div>

<script>
    const display_data = <?php echo json_encode($display_data, JSON_UNESCAPED_UNICODE) ?>;
    const everyone_preset = '<?php echo ArtFilter::EVERYONE ?>';
    // console.dir(display_data);
</script>