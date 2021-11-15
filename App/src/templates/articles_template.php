<?php
################################
## Joël Piguet - 2021.11.15 ###
##############################
?>

<div class="container">
    <h4>Articles enregistrés</h4>
    <div class="row col-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Location</th>
                    <th>Date de péremption</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article) { ?>
                    <tr>
                        <td><?php echo $article->getArticleName() ?></td>
                        <td><?php echo $article->getLocation() ?></td>
                        <td><?php echo $article->printExpirationDate() ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>