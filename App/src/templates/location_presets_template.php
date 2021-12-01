<?php
################################
## Joël Piguet - 2021.12.01 ###
##############################

use helpers\TUtil;

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
        <?php foreach ($locations as $item) { ?>
            <li class="list-group-item"><?php echo $item->getContent() ?></li>
        <?php } ?>
    </ul>

    <form method="post" action="<?php echo LOCAL_PRESETS ?>">
        <div class="input-group mt-3 mb-3">
            <label for="location-field" class="input-group-text">Nouvelle saisie</label>
            <input id="location-field" type="text" name='location-field' value="<?php echo TUtil::escape($location_field); ?>" class="form-control 
                        <?php echo isset($errors['location-field']) ? ' is-invalid' : '' ?>
                        <?php echo $location_field ? ' is-valid' : '' ?>">
            <button type="submit" name="add-new" class="btn btn-primary">Ajouter</button>
            <?php if (isset($errors['location-field'])) { ?>
                <div class='invalid-feedback'><?php echo $errors['location-field'] ?> </div>
            <?php } else { ?>
                <!-- <div id="loc-field-descr" class="form-text"> votre adresse e-mail pour vous identifier.</div> -->
            <?php } ?>

        </div>

        <a href="<?php echo ADMIN ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>