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

// $article = Database::getInstance()->getArticleById(25);

// for ($i = 0; $i < 20; $i++) {
//     $name = 'item_' . strval($i);
//     $article = Article::fromForm(1, $name, 'cuisine', '2023-11-11');
//     if (Database::getInstance()->insertArticle($article)) {
//         echo 'inserted';
//     }
// }

$item_count = Database::getInstance()->getUsersCount(false);
echo $item_count;
