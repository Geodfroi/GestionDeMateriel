<?php

################################
## Joël Piguet - 2021.11.14 ###
##############################

require_once __DIR__ . '/db_settings.php';
require_once __DIR__ . '/src/helpers/authentication.php';
require_once __DIR__ . '/src/helpers/db.php';
require_once __DIR__ . '/src/helpers/router.php';
require_once __DIR__ . '/src/helpers/template_renderer.php';
require_once __DIR__ . '/src/models/user.php';
require_once __DIR__ . '/src/routes/base_route.php';
require_once __DIR__ . '/src/routes/login_route.php';
require_once __DIR__ . '/src/routes/user_route.php';
require_once __DIR__ . '/src/routes/routes.php';


const CSS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'css';
const HELPER_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helpers';
const JS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'js';
const TEMPLATES_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'templates';
const WEB_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'web';

const DEFAULT_PAGE_TITLE = "HEdS Gestionnaire d'inventaire";
