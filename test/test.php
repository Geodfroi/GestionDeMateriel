<?php

use app\constants\LogChannel;
use app\helpers\Logging;
use app\models\Article;

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

try {
    $conn = new SQLite3('test.db');
} catch (\Throwable $th) {
    Logging::debug('Failure to establish sqlite connection.', ['msg' => $th->getMessage()], LogChannel::TEST);
    return;
}

$sql = <<<EOF
      CREATE TABLE COMPANY
      (ID INT PRIMARY KEY     NOT NULL,
      NAME           TEXT    NOT NULL,
      AGE            INT     NOT NULL,
      ADDRESS        CHAR(50),
      SALARY         REAL);
EOF;

$sql = <<<EOF
    CREATE TABLE IF NOT EXISTS articles (
    id                INTEGER         NOT NULL PRIMARY KEY AUTOINCREMENT,
    article_name      varchar(255)    NOT NULL,
    comments          varchar(255),
    creation_date     timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expiration_date   timestamp       NOT NULL,
    location          varchar(255)    NOT NULL,
    user_id           INTEGER         NOT NULL)
EOF;

$ret = $conn->exec($sql);
if (!$ret) {
    Logging::debug('table create error:' . $conn->lastErrorMsg(), [], LogChannel::TEST);
} else {
    Logging::debug('Table created successfully', [], LogChannel::TEST);
}
// $conn->close();


$article = Article::fromForm(0, 'Pneu', 'Garage', '2021-12-19');

// $sql = <<<EOF
//     INSERT INTO articles(
//         user_id, 
//         article_name, 
//         location, 
//         expiration_date
//     ) 
//     VALUES 
//     (
//         {$article->getUserID()}, 
//         '{$article->getArticleName()}', 
//         '{$article->getLocation()}', 
//         '{$article->getExpirationDate()->format('Y-m-d H:i:s')}'
//     )
// EOF;

// Logging::debug('insert statement:' . $sql, [], LogChannel::TEST);

// $ret = $conn->exec($sql);
// if (!$ret) {
//     Logging::debug('insert error:' . $conn->lastErrorMsg(), [], LogChannel::TEST);
// } else {
//     Logging::debug('Successfully inserted', [], LogChannel::TEST);
// }


$preparedStatement = $conn->prepare(
    'INSERT INTO articles 
            (
                user_id, 
                article_name, 
                location, 
                expiration_date
            ) 
            VALUES 
            (
                :uid, 
                :art, 
                :loc, 
                :date
            )'
);

$uid = $article->getUserId();
$name = $article->getArticleName();
$location = $article->getLocation();
$date = $article->getExpirationDate()->format('Y-m-d H:i:s');

$preparedStatement->bindParam(':uid', $uid, PDO::PARAM_INT);
$preparedStatement->bindParam(':art', $name, PDO::PARAM_STR);
$preparedStatement->bindParam(':loc', $location, PDO::PARAM_STR);
$preparedStatement->bindParam(':date', $date, PDO::PARAM_STR);

if ($preparedStatement->execute()) {
    // $conn->lastInsertId();
    $conn->lastInsertRowID();
    // $result = $conn->query('SELECT last_insert_rowid() as last_insert_rowid');
    // $id = $result['last_insert_rowid'];
} else {
    Logging::error('insert error', ['error' => $conn->lastErrorMsg()], LogChannel::TEST);
}
