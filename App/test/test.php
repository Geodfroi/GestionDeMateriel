<?php

require_once __DIR__ . '/../boot.php';

use helpers\Database;
use models\Article;

// $array = Database::getInstance()->getUserArticles(3, 10, 0);

// foreach ($array as $art) {
//     echo $art . PHP_EOL;
// }

$article = Article::fromForm(2, 'soap', 'cuinise', time(), '');
echo $article;
echo Database::getInstance()->insertArticle($article);
