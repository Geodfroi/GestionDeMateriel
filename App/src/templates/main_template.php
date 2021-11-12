<?php ?>

<!DOCTYPE html>
<!-- Joël Piguet - 2021.11.12 -->
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo isset($page_title) ? $page_title : "Application"; ?> </title>
    <!-- <link rel="stylesheet" href=<?php echo CSS_PATH . DIRECTORY_SEPARATOR . "bootstrap.min.css" ?> /> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <main>
        <?php
        echo isset($page_content) ? $page_content : "Erreur: le contenu n'a pas été défini pour cette page"; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- <script src=<?php echo JS_PATH . DIRECTORY_SEPARATOR .  "bootstrap.bundle.min.js" ?>></script> -->
</body>

</html>