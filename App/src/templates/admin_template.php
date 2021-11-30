<?php
################################
## Joël Piguet - 2021.11.30 ###
##############################

use helpers\UserOrder;

$page = $_SESSION[ADMIN_PAGE];

/**
 * Display caret icon besides table header to display order setting depending on _SESSION[ADMIN_ORDER_BY]
 * 
 * @param string $header Table header name
 * @return string Icon class name.
 */
function disCaretAdm(string $header): string
{
    $orderby = $_SESSION[ADMIN_ORDER_BY];
    if ($header === 'email') {
        if ($orderby === UserOrder::EMAIL_DESC) {
            return 'bi-caret-down';
        } else if ($orderby === UserOrder::EMAIL_ASC) {
            return 'bi-caret-up';
        }
    } else if ($header === 'login') {
        if ($orderby === UserOrder::LOGIN_DESC) {
            return 'bi-caret-down';
        } else if ($orderby === UserOrder::LOGIN_ASC) {
            return 'bi-caret-up';
        }
    } else if ($header === 'creation') {
        if ($orderby === UserOrder::CREATED_DESC) {
            return 'bi-caret-down';
        } else if ($orderby === UserOrder::CREATED_ASC) {
            return 'bi-caret-up';
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
function disLinkAdm(string $header): string
{
    $root = ADMIN . '?orderby=';
    $orderby = $_SESSION[ADMIN_ORDER_BY];

    // play with ASC / DESC to set default behavior the first time the column is clicked; ie creation is listed most recent first.
    if ($header === 'email') {
        return $orderby === UserOrder::EMAIL_ASC ? $root . UserOrder::EMAIL_DESC : $root . UserOrder::EMAIL_ASC;
    } else if ($header === 'login') {
        return $orderby === UserOrder::LOGIN_DESC ? $root . UserOrder::LOGIN_ASC : $root . UserOrder::LOGIN_DESC;
    } else if ($header === 'creation') {
        return $orderby === UserOrder::CREATED_DESC ? $root . UserOrder::CREATED_ASC : $root . UserOrder::CREATED_DESC;
    }
    return '';
}

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
                        <td><?php echo $user->getEmail() ?>
                            <?php if ($user->isAdmin()) { ?>
                                <i class="bi bi-hdd" aria-label="is-admin" data-bs-toggle="tooltip" title="Admin" data-bs-placement="bottom"></i>
                            <?php } ?>
                        </td>
                        <td><?php echo $user->getCreationDate()->format('d/m/Y') ?></td>
                        <td><?php echo $user->getLastLogin()->format('d/m/Y') ?></td>
                        <td>
                            <a class="link-secondary" data-bs-toggle="modal" data-bs-target=<?php echo "#delete-modal-$n" ?>><i class="bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>

                            <?php if (!$user->isAdmin()) { ?>
                                <a href="<?php echo ADMIN . '?connect=' . $user->getId() ?>" class="link-secondary text-info ms-2"><i class="bi bi-usb-plug" role="img" style="font-size: 1.2rem;" aria-label="connect-as" data-bs-toggle="tooltip" title="Se connecter en tant que [<?php echo $user->getEmail() ?>]" data-bs-placement="bottom"></i></a>
                            <?php } ?>
                        </td>
                        <!-- Modal window for user delete confirmation -->
                        <div class="modal fade" id=<?php echo "delete-modal-$n" ?> data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby=<?php echo "delete-modalLabel-$n" ?> aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id=<?php echo "delete-modalLabel-$n" ?>><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
                                    </div>
                                    <div class="modal-body">
                                        Voulez-vous vraiment supprimer [<?php echo $user->getEmail() ?>] ? Les articles associés à cet utilisateur seront également supprimés.
                                    </div>
                                    <div class="modal-footer">
                                        <a href="<?php echo ADMIN . '?delete=' . $user->getId() ?>" class="btn btn-primary">Confirmer</a>
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
                <a href="<?php echo ADMIN  . '?page=' . strval(intval($page) - 1) ?>" class="page-link" aria-label="Previous" <?php echo $page == 1 ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-left">
                    </span>
                </a>
            </li>

            <?php for ($n = 1; $n <= $page_count; $n++) {  ?>
                <li class=" page-item <?php echo $n == $page ? 'active' : '' ?>">
                    <a href="<?php echo ADMIN . '?page=' . $n ?>" class="page-link" <?php echo $n == $page ? 'tabindex = "-1"' : '' ?>><?php echo $n ?></a>
                </li>
            <?php  } ?>

            <li class="page-item  <?php echo $page == $page_count ? 'disabled' : '' ?>">
                <a href="<?php echo ADMIN . '?page=' .  strval(intval($page) + 1) ?>" class="page-link" aria-label="Next" <?php echo $page == $page_count ? 'tabindex = "-1"' : '' ?>>
                    <span aria-hidden="true" class="bi-chevron-double-right"></span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="row">
        <a href="<?php echo USER_EDIT ?>" class="btn btn-primary">Ajouter un utilisateur</a>
    </div>
</div>

<div>TODO: send email to new user when account is created.</div>
<div>TODO: alias for column created by</div>