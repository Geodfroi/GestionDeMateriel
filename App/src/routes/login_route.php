<?php

################################
## Joël Piguet - 2021.11.12 ###
##############################

namespace routes;

use db;
use helpers\Authenticate;
use function helpers\render_template;

const EMAIL_EMPTY = 'Il vous faut soumettre votre email afin de vous email';
const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe';
const EMAIL_INVALID = "L'adresse e-mail fournie est invalide";
const EMAIL_NOTFOUND = "Il n'existe pas d'usager employant cet e-mail";
const PASSWORD_INCORRECT = "Le mot de passe n'est pas correct";

class LoginRoute extends BaseRoute
{
    public function getPageContent(): string
    {
        if (Authenticate::isLoggedIn()) {
            $this->requestRedirect("/user");
            return '';
        }

        $errors = [];

        if (count($_POST)) {

            $email = trim($_POST['email']) ?? '';
            $password = trim($_POST['password']) ?? '';

            if ($email === '') {
                $errors['email'] = EMAIL_EMPTY;
            } else {
                $email = filter_var($email, FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    $errors['email'] = EMAIL_INVALID;
                }
            }

            if ($password === '') {
                $errors['password'] = PASSWORD_EMPTY;
            }

            if (count($errors) == 0) {
                // $user = db::instance()->getUserByEMail($email);
            }
        }

        return render_template("login_template", ['errors' => $errors]);
    }
}
