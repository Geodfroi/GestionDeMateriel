<?php
################################
## Joël Piguet - 2021.12.02 ###
##############################

use app\constants\Route;
use app\helpers\TUtil;

?>

<div class="container">

    <div class="row">
        <div class="col-12">
            <?php if (isset($alert['type'])) { ?>
                <div class='text-center alert alert-<?php echo $alert['type'] ?> alert-dismissible fade show' role='alert'><?php echo $alert['msg'] ?>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            <?php } ?>
        </div>
    </div>

    <ul class="list-group mt-4">
        <table class="table table-striped table-bordered align-middle">
            <tbody>
                <?php foreach ($locations as $item) { ?>

                    <?php if ($item->getId() === $selected) { ?>
                        <tr class='table-primary'>
                        <?php } else { ?>
                        <tr>
                        <?php } ?>

                        <td><?php echo $item->getContent() ?></td>

                        <?php if ($selected === 0) { ?>
                            <td>
                                <a class="link-secondary" href=<?php echo Route::LOCAL_PRESETS . '?update=' . $item->getId() ?>><i class="bi bi-pencil" role="img" style="font-size: 1.2rem;" aria-label=" update" data-bs-toggle="tooltip" title="Modifier" data-bs-placement="bottom"></i></a>
                                <a class="link-danger ms-2" data-bs-toggle="modal" data-bs-target="#delete-modal-<?php echo $item->getId() ?>"><i class=" bi bi-trash" role="img" style="font-size: 1.2rem;" aria-label="delete" data-bs-toggle="tooltip" title="Supprimer" data-bs-placement="bottom"></i></a>
                            </td>

                            <!-- Modal window for location preset delete confirmation -->
                            <div class="modal fade" id="delete-modal-<?php echo $item->getId() ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete-modalLabel-<?php echo $item->getId() ?>" aria-hidden=" true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="delete-modalLabel-<?php echo $item->getId() ?>"><i class="bi bi-exclamation-triangle text-danger"></i> Attention!</h5>
                                        </div>
                                        <div class="modal-body">
                                            Voulez-vous vraiment supprimer [<?php echo $item->getContent() ?>] ?
                                        </div>
                                        <div class="modal-footer">
                                            <a href="<?php echo Route::LOCAL_PRESETS . '?delete=' . $item->getId() ?>" class="btn btn-primary">Confirmer</a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        </tr>
                    <?php } ?>
            </tbody>
        </table>
    </ul>

    <form method="post" action="<?php echo Route::LOCAL_PRESETS ?>">

        <input type="hidden" name="id" value="<?php echo $selected ?>">

        <div class="input-group mt-3 mb-3">

            <?php if ($selected === 0) { ?>
                <label for="location" class="input-group-text"> Nouvelle saisie </label>
            <?php } ?>

            <input id="location" type="text" name='location' value="<?php echo TUtil::escape($location_field); ?>" class="form-control 
                        <?php echo isset($errors['location']) ? ' is-invalid' : '' ?>
                        <?php echo $location_field ? ' is-valid' : '' ?>">

            <?php if ($selected === 0) { ?>
                <button type="submit" name="add-new" class="btn btn-primary">Ajouter</button>
            <?php } else { ?>
                <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
            <?php } ?>

            <?php if (isset($errors['location'])) { ?>
                <div class='invalid-feedback'><?php echo $errors['location'] ?> </div>
            <?php } ?>
        </div>

        <a href="<?php echo Route::ADMIN ?>" class="btn btn-secondary">Retour</a>
    </form>
</div>