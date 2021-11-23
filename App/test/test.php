<?php

require_once __DIR__ . '/../boot.php';

use helpers\Database;
use models\Article;
use helpers\Authenticate;

// $array = Database::getInstance()->getUserArticles(3, 10, 0);

// foreach ($array as $art) {
//     echo $art . PHP_EOL;
// }

// $article = Article::fromForm(2, 'soap', 'cuisine', time(), '');

// echo Database::getInstance()->insertArticle($article);

// echo $_SERVER['REMOTE_ADDR'];

// $user_ip = Authenticate::getUserIP();
// echo $user_ip;

$article = Database::getInstance()->getArticleById(25);
