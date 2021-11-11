<?php ?>

<!DOCTYPE html>
<!-- Joël Piguet - 2021.11.11 -->
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo isset($page_title) ? $page_title : "Application"; ?> </title>
    <link rel="stylesheet" href=<?php echo CSS_PATH . DIRECTORY_SEPARATOR . "bootstrap.min.css" ?> />
</head>

<body>
    <main>

        <?php
        echo isset($page_content) ? $page_content : "Le contenu n'a pas été défini pour cette page"; ?>
    </main>

    <script src=<?php echo JS_PATH . DIRECTORY_SEPARATOR .  "bootstrap.bundle.min.js" ?>></script>
</body>

</html>