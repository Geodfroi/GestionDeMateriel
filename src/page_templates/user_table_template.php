<?php
################################
## Joël Piguet - 2022.01.19 ###
##############################

use app\constants\Requests;
use app\constants\Route;

?>

<div class="container mt-3">

    <div class="row col-12">
        <table id='table' class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th id="email-header"><a class="text-decoration-none" href="#">E-mail <span></span></a>
                    <th id="creation-header"><a class="text-decoration-none" href="#">Date de création <span></span></a>
                    <th id="last-login-header"><a class="text-decoration-none" href="#">Dernière connection <span></span></a>
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

                        <?php if (!$user->isAdmin()) { ?>
                            <td>
                                <a class="link-secondary" data-bs-toggle="modal" data-bs-target="#delete-modal" data-bs-id="<?php echo $user->getId() ?>" data-bs-email="<?php echo $user->getLoginEmail() ?>"><i class="bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>

                                <a class="link-secondary text-info ms-2" data-bs-toggle="modal" data-bs-target="#renew-modal" data-bs-id="<?php echo $user->getId() ?>" data-bs-email="<?php echo $user->getLoginEmail() ?>"><i class="bi bi-key" role="img" style="font-size: 1.2rem;" aria-label="renew-password" data-bs-toggle="tooltip" title="Renouveler le mot de passe." data-bs-placement="bottom"></i></a>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="list-pagination">
        <ul id="page-nav" class="pagination justify-content-end">

            <li id="page-last" class="page-item">
                <a href="#" class="page-link" aria-label="Previous" tabindex="-1">
                    <span aria-hidden="true" class="bi-chevron-double-left">
                    </span>
                </a>
            </li>

            <?php for ($n = 1; $n <= $page_count; $n++) {  ?>
                <li class="page-item <?php echo $n == $page ? 'active' : '' ?>">
                    <a href="<?php echo Route::USERS_TABLE . '?page=' . $n ?>" class="page-link"><?php echo $n ?></a>
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
        <a href="<?php echo Route::USER_EDIT ?>" class="btn btn-primary">Ajouter un utilisateur</a>
    </div>
</div>

<!-- Modal window for user delete confirmation -->
<div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-modal-label"><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
            </div>
            <div class="modal-body">Voulez-vous vraiment supprimer le compte utilisateur [] ? </div>
            <div class="modal-footer">
                <a href-start="<?php echo Requests::DELETE_USER ?>" class="btn btn-primary">Confirmer</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal window for renew password confirmation -->
<div class="modal fade" id="renew-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="renew-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renew-modal-label"><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
            </div>
            <div class="modal-body">Envoyer un nouveau mot de passe à [] ? </div>
            <div class="modal-footer">
                <a href-start="<?php echo Requests::RENEW_USER_PASSWORD ?>" class="btn btn-primary">Confirmer</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>