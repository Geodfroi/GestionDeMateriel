<?php
################################
## Joël Piguet - 2022.01.17 ###
##############################

use app\constants\Requests;

?>

<div class="container">

    <ul class="list-group mt-4">
        <table class="table table-striped table-bordered align-middle">
            <tbody>
                <?php foreach ($locations as $item) { ?>
                    <tr>
                        <td><?php echo $item->getContent() ?></td>
                        <td>
                            <a class="link-secondary" data-bs-toggle="modal" data-bs-target="#edit-modal" data-bs-id="<?php echo $item->getId() ?>" data-bs-content="<?php echo $item->getContent() ?>" href=""><i class="bi bi-pencil" role="img" style="font-size: 1.2rem;" aria-label="update" data-bs-toggle="tooltip" title="Modifier" data-bs-placement="bottom"></i></a>

                            <a class="link-danger ms-2" data-bs-toggle="modal" data-bs-target="#delete-modal" data-bs-id="<?php echo $item->getId() ?>" data-bs-content="<?php echo $item->getContent() ?>"><i class=" bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </ul>

    <div class="row">
        <a data-bs-toggle="modal" data-bs-target="#edit-modal" data-bs-id="" href="" class="btn btn-primary">Ajouter un preset</a>
    </div>
</div>

<!-- Modal window content edit -->
<div class="modal fade" id="edit-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 id="modal-title" class="modal-title">Nouvelle saisie. </h5>
                </div>
                <div class="modal-body">
                    <input id="id" type="hidden" name="id">
                    <input id="content" type="text" name='content' class="form-control">
                    <div id="content-feedback" class="invalid-feedback"> </div>
                </div>
                <div class="modal-footer">
                    <button id="submit-btn" type="submit" class="btn btn-primary">Ajouter</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal window for location preset delete confirmation -->
<div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-modal-label" aria-hidden=" true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-modal-label"><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
            </div>
            <div class="modal-body">
                Voulez-vous vraiment supprimer [] ?
            </div>
            <div class="modal-footer">
                <a href-start="<?php echo Requests::DELETE_LOC_PRESET ?>" href="" class="btn btn-primary">Confirmer</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>