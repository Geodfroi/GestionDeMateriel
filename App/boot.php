<?php

################################
## Joël Piguet - 2021.11.25 ###
##############################

// import files
require_once __DIR__ . '/const.php';
require_once __DIR__ . '/db_settings.php';
require_once __DIR__ . '/src/helpers/authentication.php';
// require_once __DIR__ . '/src/helpers/date_formatter.php';
require_once __DIR__ . '/src/helpers/db.php';
require_once __DIR__ . '/src/helpers/mailing.php';
require_once __DIR__ . '/src/helpers/util.php';
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
require_once __DIR__ . '/src/routes/user_edit_route.php';
require_once __DIR__ . '/src/routes/user_table_route.php';
