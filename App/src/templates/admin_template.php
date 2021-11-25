<?php
################################
## Joël Piguet - 2021.11.25 ###
##############################

use routes\Routes;
use helpers\UserOrder;

$page = $_SESSION[ADMIN_PAGE];
$orderby = $_SESSION[ADMIN_ORDER_BY];

/**
 * Display caret icon besides table header to display order setting depending on _SESSION[ADMIN_ORDER_BY]
 * 
 * @param string $header Table header name
 * @return string Icon class name.
 */
function disCaretAdm(string $header): string
{
    global $order;
    if ($header === 'email') {
        if ($order === UserOrder::EMAIL_DESC) {
            return 'bi-caret-down';
        } else if ($order === UserOrder::EMAIL_ASC) {
            return 'bi-caret-up';
        }
    } else if ($header === 'login') {
        if ($order === UserOrder::LOGIN_DESC) {
            return 'bi-caret-down';
        } else if ($order === UserOrder::LOGIN_ASC) {
            return 'bi-caret-up';
        }
    } else if ($header === 'creation') {
        if ($order === UserOrder::CREATED_DESC) {
            return 'bi-caret-down';
        } else if ($order === UserOrder::CREATED_ASC) {
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
    global $order;

    // play with ASC / DESC to set default behavior the first time the column is clicked; ie creation is listed most recent first.
    if ($header === 'email') {
        return $order === UserOrder::EMAIL_ASC ? $root . UserOrder::EMAIL_DESC : $root . UserOrder::EMAIL_ASC;
    } else if ($header === 'login') {
        return $order === UserOrder::LOGIN_DESC ? $root . UserOrder::LOGIN_ASC : $root . UserOrder::LOGIN_DESC;
    } else if ($header === 'creation') {
        return $order === UserOrder::CREATED_DESC ? $root . UserOrder::CREATED_ASC : $root . UserOrder::CREATED_DESC;
    }
    return '';
}

?>

<div class="container mt-3">
    <!-- <div class="div"> <?php echo $page ?></div> -->
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
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td><?php echo $user->getEmail() ?></td>
                        <td><?php echo $user->getCreationDate()->format('d/m/Y') ?></td>
                        <td><?php echo $user->getLastLogin()->format('d/m/Y') ?></td>
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

<div>TODO: option supprimer user</div>
<div>TODO: LOG-IN comme user</div>
<div>TODO: Créer nouvel user/password et envoyer mail au nouvel user.</div>
<div>TODO: only display non-admin user in list.</div>
<div>TODO: send email to new user when account is created.</div>