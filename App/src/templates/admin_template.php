<?php
################################
## Joël Piguet - 2021.12.01 ###
##############################

use helpers\TUtil;

?>

<div class="container">

    <div class="row" data-bs-toggle="tooltip" title="Ajouter, enlever ou modifier les données utilisateurs." data-bs-placement="bottom"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto mt-4" href="<?php echo USERS_TABLE ?>">Gérer les utilisateurs.</a></div>

    <div class="row" data-bs-toggle="tooltip" title="Modifier la liste des emplacements pouvant être sélectionnés lors de la saisie d'un nouvel article." data-bs-placement="bottom"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo ADMIN . '?update_locations=1' ?>">Gérer la liste des emplacements .</a></div>

</div>