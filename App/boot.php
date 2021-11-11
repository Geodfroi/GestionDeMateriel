<?php
################################
## Joël Piguet - 2021.11.11 ###
##############################

require_once __DIR__ . '/src/helpers/router.php';
require_once __DIR__ . '/src/helpers/template_renderer.php';
require_once __DIR__ . '/src/routes/base_route.php';
require_once __DIR__ . '/src/routes/default_route.php';

const CSS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'css';
const HELPER_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helpers';
const JS_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'js';
const TEMPLATES_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'templates';
const WWW_PAWEB_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'web';

const DEFAULT_PAGE_TITLE = "HEdS Gestionnaire d'inventaire";
