<?php

################################
## Joël Piguet - 2021.11.29 ###
##############################

use helpers\Authenticate;

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

        <nav class="navbar navbar-expand-md navbar-light bg-light">
            <div class="container-fluid">
                <span class="navbar-brand me-4" href="#">Gestionnaire d'inventaire</span>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">

                        <?php if (Authenticate::isLoggedIn()) { ?>
                            <?php if (Authenticate::isAdmin()) { ?>
                                <a class="nav-link <?php echo $_SESSION['route'] === ADMIN ? 'active' : '' ?>" href="<?php echo ADMIN ?>">Admin</a>
                            <?php } ?>

                            <a class="nav-link <?php echo $_SESSION['route'] === ART_TABLE ? 'active' : '' ?>" href="<?php echo ART_TABLE ?>">Articles</a>
                            <a class="nav-link <?php echo $_SESSION['route'] === PROFILE ? 'active' : '' ?>" href="<?php echo PROFILE ?> ">Profile</a>
                            <a class="nav-link" href="<?php echo LOGOUT ?>">Déconnexion</a>
                        <?php } else { ?>
                            <a class="nav-link" href="<?php echo LOGIN ?>">Connexion</a>
                        <?php } ?>
                    </div>
                </div>

                <!-- Display user log-in -->
                <?php if (Authenticate::isLoggedIn()) { ?>
                    <div><?php echo Authenticate::getUser()->getEmail() ?></div>
                <?php } ?>
            </div>
    </header>

    <main class="flex-shrink-0">
        <?php echo isset($page_content) ? $page_content : "Erreur: le contenu n'a pas été défini pour cette page"; ?>
    </main>

    <footer class="footer mt-auto py-3 bg-light w-100 border-up">
        <div class="container">
            <div class="row">
                <div class="text-muted h5 col-9 me-auto">HEdS - Service des innovations pédagogiques.</div>
                <div class="col-3 text-end"> <a href="/contact">Contacter-nous.</a></div>
            </div>
            <div class="row">
                <span class="text-muted h6 col-12"><?php echo LAST_MODIFICATION ?></span>
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