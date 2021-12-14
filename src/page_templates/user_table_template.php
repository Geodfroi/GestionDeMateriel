<?php
################################
## Joël Piguet - 2021.12.09 ###
##############################

use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;

$page = $_SESSION[Session::USERS_PAGE];

/**
 * Display caret icon besides table header to display order setting depending on _SESSION[USERS_ORDERBY]
 * 
 * @param string $header Table header name
 * @return string Icon class name.
 */
function disCaretAdm(string $header): string
{
    $orderby = $_SESSION[Session::USERS_ORDERBY];
    if ($header === 'email') {
        if ($orderby === OrderBy::EMAIL_DESC) {
            return 'bi-caret-down';
        } else if ($orderby === OrderBy::EMAIL_ASC) {
            return 'bi-caret-up';
        }
    } else if ($header === 'login') {
        if ($orderby === OrderBy::LOGIN_DESC) {
            return 'bi-caret-down';
        } else if ($orderby === OrderBy::LOGIN_ASC) {
            return 'bi-caret-up';
        }
    } else if ($header === 'creation') {
        if ($orderby === OrderBy::CREATED_DESC) {
            return 'bi-caret-down';
        } else if ($orderby === OrderBy::CREATED_ASC) {
            return 'bi-caret-up';
        }
    }
    return '';
}

/**
 * Compile header link depending on Session USERS_ORDERBY
 * 
 * @param string $header Table header name
 * @return string Header link to display in href.
 */
function disLinkAdm(string $header): string
{
    $root = Route::USERS_TABLE . '?orderby=';
    $orderby = $_SESSION[Session::USERS_ORDERBY];

    // play with ASC / DESC to set default behavior the first time the column is clicked; ie creation is listed most recent first.
    if ($header === 'email') {
        return $orderby === OrderBy::EMAIL_ASC ? $root . OrderBy::EMAIL_DESC : $root . OrderBy::EMAIL_ASC;
    } else if ($header === 'login') {
        return $orderby === OrderBy::LOGIN_DESC ? $root . OrderBy::LOGIN_ASC : $root . OrderBy::LOGIN_DESC;
    } else if ($header === 'creation') {
        return $orderby === OrderBy::CREATED_DESC ? $root . OrderBy::CREATED_ASC : $root . OrderBy::CREATED_DESC;
    }
    return '';
}

?>

<div class="container mt-3">

    <div class="row col-12">
        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th><a class="text-decoration-none" href="<?php echo disLinkAdm('email') ?>">E-mail <span class="<?php echo disCaretAdm('email') ?>"></span></a>
                    <th><a class="text-decoration-none" href="<?php echo disLinkAdm('creation') ?>">Date de création <span class="<?php echo disCaretAdm('creation') ?>"></span></a>
                    <th><a class="text-decoration-none" href="<?php echo disLinkAdm('login') ?>">Dernière connection <span class="<?php echo disCaretAdm('login') ?>"></span></a>

                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($n = 0; $n < count($users); $n++) {
                    $user = $users[$n]; ?>
                    <tr>
                        <!-- email -->
                        <td>
                            <?php echo $user->getAlias(); ?>
                            <?php if ($user->isAdmin()) { ?>
                                <i class="bi bi-hdd" aria-label="is-admin" data-bs-toggle="tooltip" title="Admin" data-bs-placement="bottom"></i>
                            <?php } ?>
                        </td>
                        <!-- creation -->
                        <td><?php echo $user->getCreationDate()->format('d-m-Y') ?></td>
                        <!-- login -->
                        <td><?php echo $user->getLastLogin()->format('d-m-Y H:i:s') ?></td>
                        <td>
                            <a class="link-secondary" data-bs-toggle="modal" data-bs-target=<?php echo "#delete-modal-$n" ?>><i class="bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>

                            <?php if (!$user->isAdmin()) { ?>
                                <a class="link-secondary text-info ms-2" data-bs-toggle="modal" data-bs-target=<?php echo "#renew-modal-$n" ?>><i class="bi bi-key" role="img" style="font-size: 1.2rem;" aria-label="renew-password" data-bs-toggle="tooltip" title="Renouveler le mot de passe." data-bs-placement="bottom"></i></a>
                            <?php } ?>
                        </td>

                        <!-- Modal window for user delete confirmation -->
                        <div class="modal fade" id=<?php echo "delete-modal-$n" ?> data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby=<?php echo "delete-modal-label-$n" ?> aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id=<?php echo "delete-modal-label-$n" ?>><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
                                    </div>
                                    <div class="modal-body"> Voulez-vous vraiment supprimer le compte utilisateur [<?php echo $user->getLoginEmail() ?>] ? </div>
                                    <div class="modal-footer">
                                        <a href="<?php echo Route::USERS_TABLE . '?delete=' . $user->getId() ?>" class="btn btn-primary">Confirmer</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal window for renew password confirmation -->
                        <div class="modal fade" id=<?php echo "renew-modal-$n" ?> data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby=<?php echo "renew-modal-label-$n" ?> aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id=<?php echo "renew-modal-label-$n" ?>><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
                                    </div>
                                    <div class="modal-body">Envoyer un nouveau mot de passe à [<?php echo $user->getLoginEmail() ?>] ? </div>
                                    <div class="modal-footer">
                                        <a href="<?php echo Route::USERS_TABLE . '?renew=' . $user->getId() ?>" class="btn btn-primary">Confirmer</a>
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
                <a href="<?php echo Route::USERS_TABLE  . '?page=' . strval(intval($page) - 1) ?>" class="page-link" aria-label="Previous" <?php echo $page == 1 ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-left">
                    </span>
                </a>
            </li>

            <?php for ($n = 1; $n <= $page_count; $n++) {  ?>
                <li class=" page-item <?php echo $n == $page ? 'active' : '' ?>">
                    <a href="<?php echo Route::USERS_TABLE . '?page=' . $n ?>" class="page-link" <?php echo $n == $page ? 'tabindex = "-1"' : '' ?>><?php echo $n ?></a>
                </li>
            <?php  } ?>

            <li class="page-item  <?php echo $page == $page_count ? 'disabled' : '' ?>">
                <a href="<?php echo Route::USERS_TABLE . '?page=' .  strval(intval($page) + 1) ?>" class="page-link" aria-label="Next" <?php echo $page == $page_count ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-right"></span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="row">
        <a href="<?php echo Route::USER_EDIT ?>" class="btn btn-primary">Ajouter un utilisateur</a>
    </div>
</div>