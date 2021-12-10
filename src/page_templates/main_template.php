<?php

################################
## Joël Piguet - 2021.12.07 ###
##############################

use app\constants\Route;
use app\constants\Settings;
use app\helpers\Authenticate;

?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo isset($page_title) ? $page_title : "Application"; ?> </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
</head>

<body class="d-flex flex-column h-100">

    <header>

        <nav class="navbar navbar-expand-md navbar-light bg-light fixed-top">
            <div class="container-fluid">
                <span class="navbar-brand me-4" href="#">HEdS - Gestionnaire d'inventaire</span>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <ul class="navbar-nav">

                        <?php if (Authenticate::isLoggedIn()) { ?>
                            <?php if (Authenticate::isAdmin()) { ?>
                                <li class="nav-item active">
                                    <a class="nav-link <?php echo $_SESSION['route'] === Route::ADMIN ? 'active' : '' ?>" href="<?php echo Route::ADMIN ?>">Admin</a>
                                </li>
                            <?php } ?>
                            <li class="nav-item active">
                                <a class="nav-link <?php echo $_SESSION['route'] === Route::ART_TABLE ? 'active' : '' ?>" href="<?php echo Route::ART_TABLE ?>">Articles</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link <?php echo $_SESSION['route'] === Route::PROFILE ? 'active' : '' ?>" href="<?php echo Route::PROFILE ?> ">Profile</a>
                            </li>
                        <?php } ?>

                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item ms-auto">
                            <?php if (Authenticate::isLoggedIn()) { ?>
                                <div>
                                    <a class="nav-link" href="<?php echo Route::LOGOUT ?>"><?php echo Authenticate::getUser()->getEmail() . '  ' ?><i class="bi bi-box-arrow-in-right"></i></a>
                                </div>
                            <?php } else { ?>
                                <a class="nav-link" href="<?php echo Route::LOGIN ?>">Connexion</a>
                            <?php } ?>
                        </li>
                    </ul>
                </div>
    </header>

    <main class="flex-shrink-0 mt-5">
        <?php echo isset($page_content) ? $page_content : "Erreur: le contenu n'a pas été défini pour cette page"; ?>
    </main>

    <div>
        <div>BUG: when user is deleted, its created articles dont show anymore</div>
        <div>EDIT USER</div>
        <div>PROFILE - Renew user password from ADMIN account</div>
        <div>MAIN: backup db</div>
        <div>ADMIN - access user contact posts.</div>
        <div>ADMIN - create new email to user.</div>
        <div>ARTICLES: filter with created by</div>
        <div>ARTICLES: fixed column size</div>
        <div>ARTICLES: better adaptive layout</div>
        <div>CONTACT: create contact page</div>
        <div>LOGIN: add favicon</div>
        <div>USER_EDIT: layout for small screen.</div>
        <div>USER_TABLE: send email to new user when account is created.</div>
        <div>USER_TABLE: alias for column created by</div>
    </div>

    <div style="margin-top: 80px;">&nbsp;</div>
    <footer class="footer mt-auto py-3 bg-light w-100 border-up fixed-bottom">
        <div class="container">
            <div class="row">
                <div class="text-muted h5 col-9 me-auto">HEdS - Service des innovations pédagogiques.</div>
                <div class="col-3 text-end"> <a href="/contact">Contacter-nous.</a></div>
            </div>
            <div class="row">
                <span class="text-muted h6 col-12"><?php echo Settings::LAST_MODIFICATION ?></span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        // activate tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>

</html>