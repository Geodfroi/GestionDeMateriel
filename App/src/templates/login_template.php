<div class="d-flex justify-content-center">
    <!-- Joël Piguet - 2021.11.12 -->

    <form method="post" action="/login" class="w-50">

        <label class="h4 m-4">Formulaire de connexion</label>
        <div class="mb-3">
            <label for="form-email" class="form-label">Adresse e-mail</label>
            <input id="form-email" class="form-control" type="email" name="email" aria-describedby="id-descr" value=<?php
                                                                                                                    echo "aaabbb";
                                                                                                                    ?>>
            <div id="id-descr" class="form-text">Entrer votre adresse e-mail pour vous identifier.</div>
        </div>
        <div class="mb-3">
            <label for="form-password" class="form-label">Mot de passe</label>
            <input id="form-password" class="form-control" type="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Transmettre</button>
    </form>
</div>