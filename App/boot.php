<?php

################################
## Joël Piguet - 2021.11.15 ###
##############################

require_once __DIR__ . '/db_settings.php';
require_once __DIR__ . '/src/helpers/authentication.php';
require_once __DIR__ . '/src/helpers/db.php';
require_once __DIR__ . '/src/helpers/mailing.php';
require_once __DIR__ . '/src/helpers/router.php';
require_once __DIR__ . '/src/helpers/template_renderer.php';
require_once __DIR__ . '/src/models/article.php';
require_once __DIR__ . '/src/models/user.php';

require_once __DIR__ . '/src/routes/base_route.php'; // must be included before other routes as it contains the route base class.
require_once __DIR__ . '/src/routes/admin_route.php';
require_once __DIR__ . '/src/routes/articles_route.php';
require_once __DIR__ . '/src/routes/login_route.php';
require_once __DIR__ . '/src/routes/profile_route.php';
require_once __DIR__ . '/src/routes/routes.php';

const CSS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'css';
const HELPER_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helpers';
const JS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'js';
const TEMPLATES_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'templates';
const WEB_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'web';

const APP_NAME = "HEdS Gestionnaire d'inventaire";
const ADMIN_EMAIL = "aurore.azure@gmail.com";
