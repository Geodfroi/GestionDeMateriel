<?php
################################
## Joël Piguet - 2022.05.03 ###
##############################

use app\constants\Route;

?>

<div class="container mt-3">

    <div class="row justify-content-center d-md-none mb-1">
        <label class="text-center h4 ">Utilisateurs</label>
    </div>

    <div class="row justify-content-center mx-auto mb-3">
        <a class="btn btn-primary col-12 mx-auto d-lg-none" href="<?php echo Route::USER_EDIT ?>">Ajouter un utilisateur</a>
    </div>

    <div class="row mx-auto">
        <table id='table' class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th scope="col" id="email-header">
                        <a class="text-decoration-none" href="#">
                            <span>Alias</span>
                            <span class="icon"></span>
                        </a>
                    <th class="d-none d-lg-table-cell" scope="col" id="creation-header">
                        <a class="text-decoration-none" href="#">
                            <span>Date de création</span>
                            <span class="icon"></span>
                        </a>
                    <th scope="col" id="last-login-header">
                        <a class="text-decoration-none" href="#">
                            <span class="d-none d-lg-inline">Dernière connection</span>
                            <span class="d-inline d-lg-none">Dern. conn.</span>
                            <span class="icon"></span>
                        </a>
                    <th scope="col" class="d-none d-lg-table-cell">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($n = 0; $n < count($users); $n++) {
                    $user = $users[$n]; ?>
                    <tr class="table-row " data-bs-admin="<?php echo $user->isAdmin(); ?>" data-bs-id="<?php echo $user->getId() ?>" data-bs-email="<?php echo $user->getLoginEmail() ?>">
                        <!-- alias -->
                        <td>
                            <?php echo $user->getAlias(); ?>
                            <?php if ($user->isAdmin()) { ?> <i class=" bi bi-hdd" aria-label="is-admin" data-bs-toggle="tooltip" title="Admin" data-bs-placement="bottom"></i>
                            <?php } ?>

                        </td>
                        <!-- creation -->
                        <td class="d-none d-lg-table-cell"><?php echo $user->getCreationDate()->format('d-m-Y') ?></td>
                        <!-- login -->
                        <td><?php echo $user->getLastLogin()->format('d-m-Y H:i:s') ?></td>

                        <!-- Actions -->
                        <?php if (!$user->isAdmin()) { ?>
                            <td class="d-none d-lg-table-cell">
                                <a id="delete-link" class="link-secondary" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>

                                <a id="renew-link" class="link-secondary text-info ms-2" data-bs-toggle="modal" data-bs-target="#renew-modal"><i class="bi bi-key" role="img" style="font-size: 1.2rem;" aria-label="renew-password" data-bs-toggle="tooltip" title="Renouveler le mot de passe." data-bs-placement="bottom"></i></a>
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

            <?php for ($n = 1; $n <= $display_data['page_count']; $n++) {  ?>
                <li class="page-item <?php echo $n ==  $display_data['page']  ? 'active' : '' ?>">
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

    <div class="row-12 d-none d-lg-block">
        <a href="<?php echo Route::USER_EDIT ?>" class="btn btn-primary col-12 mx-auto">Ajouter un utilisateur</a>
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
                <a href="#" class="btn btn-primary">Confirmer</a>
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
                <a href="#" class="btn btn-primary">Confirmer</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="action-modal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="filter-modal-label" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <span class="h5 mx-auto">Intéragir avec []</span>
            </div>
            <div class="modal-body">
                <div class="row"><a id="renew-btn" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#renew-modal">Renouveler le mot de passe.</a></div>
                <div class="row mt-2"><a id="delete-btn" type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#delete-modal">Supprimer.</a></div>
                <div class="row mt-2"><a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</a></div>
            </div>
        </div>
    </div>
</div>

<script>
    let display_data = <?php echo json_encode($display_data, JSON_UNESCAPED_UNICODE) ?>;
</script>