<?php
################################
## Joël Piguet - 2021.11.25 ###
##############################

use routes\Routes;
use helpers\ArtOrder;

$page = $_SESSION[ART_PAGE];
$orderby = $_SESSION[ART_ORDER_BY];

/**
 * Display caret icon besides table header to display order setting depending on _SESSION[ADMIN_ORDER_BY]
 * 
 * @param string $header Table header name
 * @return string Icon class name.
 */
function disCaretArt(string $header): string
{
    global $order;

    if ($header === 'article') {
        if ($order === ArtOrder::NAME_ASC) {
            return 'bi-caret-up';
        } else if ($order === ArtOrder::NAME_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'location') {
        if ($order === ArtOrder::LOCATION_ASC) {
            return 'bi-caret-up';
        } else if ($order === ArtOrder::LOCATION_DESC) {
            return 'bi-caret-down';
        }
    } else if ($header === 'per_date') {
        if ($order === ArtOrder::DATE_ASC) {
            return 'bi-caret-up';
        } else if ($order === ArtOrder::DATE_DESC) {
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
    global $order;

    // play with ASC / DESC to set default behavior the first time the column is clicked; ie per_date is listed most recent first.
    if ($header === 'article') {
        return $order === ArtOrder::NAME_ASC ? $root . ArtOrder::NAME_DESC : $root . ArtOrder::NAME_ASC;
    } else if ($header === 'location') {
        return $order === ArtOrder::LOCATION_ASC ? $root . ArtOrder::LOCATION_DESC : $root . ArtOrder::LOCATION_ASC;
    } else if ($header === 'per_date') {
        return $order === ArtOrder::DATE_DESC ? $root . ArtOrder::DATE_ASC : $root . ArtOrder::DATE_DESC;
    }
    return '';
}

?>

<div class="container mt-3">

    <!-- <button class="btn btn-secondary" data-bs-toggle="tooltip" title="Tooltip on bottom" data-bs-placement="bottom">aaa</button> -->
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
                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('article') ?>">Article <span class="<?php echo disCaretArt('article') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('location') ?>">Location <span class="<?php echo disCaretArt('location') ?>"></span></a>

                    <th><a class="text-decoration-none" href="<?php echo disLinkArt('per_date') ?>">Date de péremption <span class="<?php echo disCaretArt('per_date') ?>"></span></a>

                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article) { ?>
                    <tr>
                        <td><?php echo $article->getArticleName() ?></td>
                        <td><?php echo $article->getLocation() ?></td>
                        <td><?php echo $article->getExpirationDate()->format('d/m/Y') ?></td>
                        <td><span class="bi-text-left <?php echo strlen($article->getComments()) == 0 ? 'text-secondary' : 'text-info' ?>" data-bs-toggle="tooltip" title="<?php echo $article->getComments() ?>" data-bs-placement="right"></span></td>
                        <td>
                            <a class="link-secondary" href=<?php echo ART_EDIT . '?update=' . $article->getId() ?>><i class="bi-pencil" role="img" style="font-size: 1.2rem;" aria-label=" update" data-bs-toggle="tooltip" title="Editer" data-bs-placement="bottom"></i></a>
                            <span style=" font-size: 1.2rem;">|</span>
                            <a class="link-danger" href=<?php echo ART_TABLE . '?delete=' . $article->getId() ?>><i class="bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Effacer" data-bs-placement="bottom"></i></a>
                        </td>
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


<div>TODO: fixed column size</div>
<div>TODO: better adaptive layout</div>
<div>TODO: put tab logout link under email on the left</div>
<div>TODO: confirm delete</div>

<!-- <th>
                        <?php if ($orderby ===  ArtOrder::NAME_DESC) { ?><a href="<?php echo ART_TABLE . '?orderby=' . ArtOrder::NAME_ASC ?> "><i class="bi-caret-down" style="font-size: 1.2rem;"></i><a>
                                <?php } else if ($orderby ===  ArtOrder::NAME_ASC) { ?>
                                    <a href="<?php echo ART_TABLE . '?orderby=' . ArtOrder::NAME_DESC  ?> "><i class="bi-caret-up" style="font-size: 1.2rem"></i><a>
                                        <?php } else { ?>
                                            <a href="<?php echo ART_TABLE . '?orderby=' . ArtOrder::NAME_ASC ?> "><i class="bi-circle" style="font-size: 1.0rem;"></ <?php } ?> </th>