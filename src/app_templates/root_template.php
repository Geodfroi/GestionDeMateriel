<?php

################################
## Joël Piguet - 2022.04.29 ###
##############################

use app\constants\Page;
use app\constants\Route;
use app\constants\Session;
use app\helpers\Authenticate;

?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo isset($page_title) ? $page_title : "Application"; ?> </title>

    <link rel="shortcut icon" href="<?php echo $_SESSION[Session::ROOT] ?>/favicon.ico" type="image/x-icon" />

    <!-- bootstrap css from static folder -->
    <link rel="stylesheet" href="<?php echo $_SESSION[Session::ROOT] ?>/static/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $_SESSION[Session::ROOT] ?>/static/css/bootstrap-icons.css" />

    <!-- bootstrap css by CDN -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css"> -->
</head>

<body class="d-flex flex-column h-100">

    <?php if ($show_header) { ?>
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
                <div class="container-fluid">
                    <span class="navbar-brand me-4 d-lg-none" href="#"><?php echo strlen(APP_NAME) > 12 ? substr(APP_NAME, 0, 12) . '..' : APP_NAME ?></span>
                    <span class="navbar-brand me-4 d-none d-lg-inline" href="#"><?php echo APP_NAME ?></span>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <ul class="navbar-nav">

                            <?php if (Authenticate::isLoggedIn()) { ?>
                                <?php if (Authenticate::isAdmin()) { ?>
                                    <li class="nav-item active">
                                        <a class="nav-link <?php echo $_SESSION[Session::PAGE] === Page::ADMIN ? 'active' : '' ?>" href="<?php echo Route::ADMIN ?>">Admin</a>
                                    </li>
                                <?php } ?>
                                <li class="nav-item active">
                                    <a class="nav-link <?php echo $_SESSION[Session::PAGE] === Page::ART_TABLE ? 'active' : '' ?>" href="<?php echo Route::ART_TABLE ?>">Articles</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link <?php echo $_SESSION[Session::PAGE] === Page::PROFILE ? 'active' : '' ?>" href="<?php echo Route::PROFILE ?> ">Profil</a>
                                </li>
                            <?php } ?>

                            <?php if (DEBUG_MODE) { ?>
                                <a class="nav-link <?php echo $_SESSION[Session::PAGE_URL] === Page::DEBUG_PAGE ? 'active' : '' ?>" href="<?php echo Route::DEBUG_PAGE ?> ">[debug]</a>
                            <?php } ?>

                        </ul>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item ms-auto">
                                <?php if (Authenticate::isLoggedIn()) { ?>
                                    <div>
                                        <a class="nav-link" href="<?php echo Route::LOGOUT ?>"><?php echo Authenticate::getUser()->getLoginEmail() . '  ' ?><i class="bi bi-box-arrow-in-right"></i></a>
                                    </div>
                                <?php } else { ?>
                                    <a class="nav-link" href="<?php echo Route::LOGIN ?>">Connexion</a>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- show alert message over navbar -->
            <?php if (isset($alert['type'])) { ?>
                <div class="row fixed-top">
                    <div class="col-12">
                        <div class='text-center alert alert-<?php echo $alert['type'] ?> alert-dismissible fade show' role='alert'><?php echo $alert['msg'] ?>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </header>
    <?php } ?>

    <main class="flex-shrink-0 mt-5">
        <?php echo isset($page_content) ? $page_content : "Erreur: le contenu n'a pas été défini pour cette page."; ?>
    </main>

    <?php if ($show_footer) { ?>
        <div style="margin-top: 80px;">&nbsp;</div>
        <footer class="footer mt-auto py-3 bg-light w-100 border-up fixed-bottom">
            <div class="container">
                <div class="row">
                    <div class="text-muted h5 col-9 me-auto">HEdS - Service des innovations pédagogiques.</div>
                    <div class="col-3 text-end"> <a href="mailto:<?php echo CONTACT_EMAIL ?>">Contacter-nous.</a></div>
                </div>
                <div class="row">
                    <span class="text-muted h6 col-12"><?php echo LAST_MODIFICATION ?></span>
                </div>
            </div>
        </footer>
    <?php } ?>

    <!-- bootstrap javascript from static folder -->
    <script src="<?php echo $_SESSION[Session::ROOT] ?>/static/js/bootstrap.bundle.min.js"> </script>

    <!-- bootstrap javascript by CDN -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->

    <script src="<?php echo $_SESSION[Session::ROOT] ?>/static/js/bs-detect-breakpoints.js"></script>

    <!-- bootstrap javascript to activate tooltips -->
    <script>
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    <!-- custom scripts -->
    <script>
        let root_url = "<?php echo $_SESSION[Session::ROOT] ?>";
        let page_url = "<?php echo $_SESSION[Session::PAGE_URL] ?>";
    </script>
    <script src="<?php echo $_SESSION[Session::ROOT] ?>/static/js/main-script.js"></script>

    <?php if ($script_name) { ?>
        <script src="<?php echo $_SESSION[Session::PAGE_URL] . '/' . $script_name . '.js' ?>"></script>
    <?php } ?>

    <!-- dismiss alert after timer -->
    <?php if (isset($alert['type'])) { ?>
        <script>
            let alert = document.getElementsByClassName('alert')[0];
            let time = <?php echo $alert['timer'] ?>;
            window.setTimeout(() => {
                // alert.classList.remove('show');
                alert.style.display = 'None';
            }, time);
        </script>
    <?php } ?>
</body>

</html>