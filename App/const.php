<?php

################################
## Joël Piguet - 2021.11.25 ###
##############################

// App constants
const APP_NAME = "HEdS Gestionnaire d'inventaire";
const ADMIN_EMAIL = "aurore.azure@gmail.com";
const TEMPLATES_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'templates';

const ARTICLE_NAME_MIN_LENGHT = 6;
const ARTICLE_NAME_MAX_LENGTH = 20;
const ARTICLE_LOCATION_MIN_LENGHT = 6;
const ARTICLE_LOCATION_MAX_LENGHT = 40;
const ARTICLE_COMMENTS_MAX_LENGHT = 240;
const ARTICLE_DATE_FUTURE_LIMIT = '2050-01-01';

const DEFAULT_PASSWORD_LENGTH = 12;
const USER_PASSWORD_MIN_LENGTH = 8;

// routes
const ADMIN = '/admin';
const ART_TABLE = '/articlesList';
const ART_EDIT = '/articleEdit';
const CONTACT = '/contact';
const LOGIN = '/login';
const LOGOUT = '/login?logout=true';
const HOME = '/';
const PROFILE = '/profile';
const USER_EDIT = '/userEdit';


// SESSION global variables keys.
const ADMIN_ORDER_BY = 'admin_orderby';
const ADMIN_PAGE = 'admin_page';
const ART_ORDER_BY = 'articles_orderby';
const ART_PAGE = 'articles_page';

const ADMIN_ID = 'admin_id';
const USER_ID = 'user_id';

// const COOKIE_NAME = 'cookie_user';
// const COOKIE_HOURS = 2 * 7 * 24 * 60 * 60; // cookie expires after two weeks by default;

// const CSS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'css';
// const HELPER_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helpers';
// const JS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'js';
// const WEB_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'web';