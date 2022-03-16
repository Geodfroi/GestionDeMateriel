<?php

################################
## Joël Piguet - 2022.03.15 ###
##############################

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/_local/localsettings.php';

require_once __DIR__ . '/src/constants/alert.php';
require_once __DIR__ . '/src/constants/alerttype.php';
require_once __DIR__ . '/src/constants/apppaths.php';
require_once __DIR__ . '/src/constants/artfilter.php';
require_once __DIR__ . '/src/constants/logerror.php';
require_once __DIR__ . '/src/constants/loginfo.php';
require_once __DIR__ . '/src/constants/mail.php';
require_once __DIR__ . '/src/constants/orderby.php';
require_once __DIR__ . '/src/constants/requests.php';
require_once __DIR__ . '/src/constants/route.php';
require_once __DIR__ . '/src/constants/session.php';
require_once __DIR__ . '/src/constants/warning.php';

require_once __DIR__ . '/src/helpers/db/queries.php'; // base class must be first in paragraph
require_once __DIR__ . '/src/helpers/db/articlequeries.php';
require_once __DIR__ . '/src/helpers/db/locationqueries.php';
require_once __DIR__ . '/src/helpers/db/userqueries.php';

require_once __DIR__ . '/src/helpers/authenticate.php';
require_once __DIR__ . '/src/helpers/convert.php';
require_once __DIR__ . '/src/helpers/database.php';
require_once __DIR__ . '/src/helpers/dbutil.php';
require_once __DIR__ . '/src/helpers/logging.php';
require_once __DIR__ . '/src/helpers/mailing.php';
require_once __DIR__ . '/src/helpers/requestutil.php';
require_once __DIR__ . '/src/helpers/util.php';
require_once __DIR__ . '/src/helpers/validation.php';

require_once __DIR__ . '/src/models/article.php';
require_once __DIR__ . '/src/models/stringcontent.php';
require_once __DIR__ . '/src/models/user.php';

require_once __DIR__ . '/src/routes/baseroute.php'; // base class must be first in paragraph
require_once __DIR__ . '/src/routes/admin.php';
// require_once __DIR__ . '/src/routes/articleedit.php';
require_once __DIR__ . '/src/routes/articletable.php';
require_once __DIR__ . '/src/routes/contact.php';
require_once __DIR__ . '/src/routes/debugemails.php';
require_once __DIR__ . '/src/routes/locationlist.php';
// require_once __DIR__ . '/src/routes/login.php';
require_once __DIR__ . '/src/routes/profile.php';
require_once __DIR__ . '/src/routes/useredit.php';
require_once __DIR__ . '/src/routes/usertable.php';
