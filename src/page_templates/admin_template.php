<?php
################################
## Joël Piguet - 2022.01.20 ###
##############################

use app\constants\Route;

?>

<div class="container">

    <div class="row justify-content-center d-md-none">
        <label class="text-center h4 mt-3 ">Admin</label>
    </div>

    <div class="row mx-auto" data-bs-toggle="tooltip" title="Ajouter, enlever ou modifier les données utilisateurs." data-bs-placement="bottom"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto mt-4" href="<?php echo Route::USERS_TABLE ?>">Gérer les utilisateurs.</a></div>

    <div class="row mx-auto" data-bs-toggle="tooltip" title="Modifier la liste des emplacements pouvant être sélectionnés lors de la saisie d'un nouvel article." data-bs-placement="bottom"> <a class="btn btn-outline-primary mb-3 col-12 col-md-6 mx-auto" href="<?php echo Route::LOCAL_PRESETS ?>">Gérer la liste des emplacements .</a></div>

</div>