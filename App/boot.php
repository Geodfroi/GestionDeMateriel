<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

// import files
require_once __DIR__ . '/db_settings.php';
require_once __DIR__ . '/src/helpers/authentication.php';
require_once __DIR__ . '/src/helpers/date_formatter.php';
require_once __DIR__ . '/src/helpers/db.php';
require_once __DIR__ . '/src/helpers/mailing.php';
require_once __DIR__ . '/src/helpers/template_renderer.php';
require_once __DIR__ . '/src/helpers/template_util.php';
require_once __DIR__ . '/src/models/article.php';
require_once __DIR__ . '/src/models/user.php';

require_once __DIR__ . '/src/routes/base_route.php'; // must be included before other routes as it contains the route base class.
require_once __DIR__ . '/src/routes/admin_route.php';
require_once __DIR__ . '/src/routes/articles_table_route.php';
require_once __DIR__ . '/src/routes/article_edit_route.php';
require_once __DIR__ . '/src/routes/contact_route.php';
require_once __DIR__ . '/src/routes/home_route.php';
require_once __DIR__ . '/src/routes/login_route.php';
require_once __DIR__ . '/src/routes/profile_route.php';
require_once __DIR__ . '/src/routes/routes.php';


// App constants
const APP_NAME = "HEdS Gestionnaire d'inventaire";
const ADMIN_EMAIL = "aurore.azure@gmail.com";
const TEMPLATES_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'templates';

// SESSION global variables keys.

const ADMIN_ORDER_BY = 'admin_orderby';
const ADMIN_PAGE = 'admin_page';
const ART_ORDER_BY = 'articles_orderby';
const ART_PAGE = 'articles_page';
const USER_ID = 'user_id';
const USER_IS_ADMIN = 'is_admin';


// const COOKIE_NAME = 'cookie_user';
// const COOKIE_HOURS = 2 * 7 * 24 * 60 * 60; // cookie expires after two weeks by default;

// const CSS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'css';
// const HELPER_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helpers';
// const JS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'js';
// const WEB_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'web';