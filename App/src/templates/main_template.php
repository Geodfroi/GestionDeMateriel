<?php

################################
## Joël Piguet - 2021.11.22 ###
##############################

use helpers\Authenticate;
use routes\Routes;

/**
 * Print active class tag if the param is the current route.
 * 
 * @param string $route: Route argument.
 * @return string return 'active' or empty string;
 */
function setActive(string $route): string
{
    if (isset($_SERVER['PATH_INFO']) && ($_SERVER['PATH_INFO'] === $route)) {
        return 'active';
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo isset($page_title) ? $page_title : "Application"; ?> </title>
    <!-- <link rel="stylesheet" href=<?php echo CSS_PATH . DIRECTORY_SEPARATOR . "bootstrap.min.css" ?> /> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
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
                        <a class="nav-link <?php echo setActive(Routes::ARTICLES) ?>" href="<?php echo Routes::ARTICLES ?>">Articles</a>
                        <a class="nav-link <?php echo setActive(Routes::PROFILE) ?>" href="<?php echo Routes::PROFILE ?> ">Profile</a>
                        <a class="nav-link <?php echo setActive(Routes::CONTACT) ?>" href="<?php echo Routes::CONTACT ?>">Contact</a>

                        <?php if (Authenticate::isLoggedIn()) { ?>
                            <a class="nav-link" href="<?php echo Routes::LOGOUT ?>">Déconnexion</a>
                        <?php } else { ?>
                            <a class="nav-link" href="<?php echo Routes::LOGIN ?>">Connexion</a>
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
        <?php
        echo isset($page_content) ? $page_content : "Erreur: le contenu n'a pas été défini pour cette page"; ?>
    </main>

    <footer class="footer mt-auto py-3 bg-light w-100 border-up">
        <div class="container">
            <div class="row">
                <div class="text-muted h5 col-9 me-auto">HEdS - Service des innovations pédagogiques.</div>
                <div class="col-3 text-end"> <a href="/contact">Contacter-nous.</a></div>
            </div>
            <div class="row">
                <span class="text-muted h6 col-12">16 novembre 2021</span>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- <script src=<?php echo JS_PATH . DIRECTORY_SEPARATOR .  "bootstrap.bundle.min.js" ?>></script> -->

</body>

</html>