<?php

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace routes;

use helpers\Database;
use helpers\Authenticate;
use function helpers\render_template;

const EMAIL_EMPTY = 'Un e-mail est nécessaire pour vous connecter.';
const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
const EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
const EMAIL_NOTFOUND = "Il n'existe pas d'usager employant cette adresse e-mail.";
const PASSWORD_INVALID = "Le mot de passe n'est pas correct.";


class LoginRoute extends BaseRoute
{
    public function getPageContent(): string
    {
        if (Authenticate::isLoggedIn()) {
            $this->requestRedirect("/user");
            return '';
        }

        $form_errors = [];
        $email  = '';
        if (count($_POST)) {

            $email  = trim($_POST['email']) ?? '';
            $password = trim($_POST['password']) ?? '';
            $user = '';

            if ($email  === '') {
                $form_errors['email'] = EMAIL_EMPTY;
            } else {
                $email = filter_var($email, FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    $form_errors['email'] = EMAIL_INVALID;
                } else {
                    if ($user = Database::getInstance()->getUserByEMail($email)) {
                    } else {
                        $form_errors['email'] = EMAIL_NOTFOUND;
                    }
                }
            }

            if ($password === '') {
                $form_errors['password'] = PASSWORD_EMPTY;
            } else 
            if (count($form_errors) == 0) {
                $form_errors['password'] = PASSWORD_INVALID;
                #check password in the absence of form_errors.
                // $user = db::instance()->getUserByEMail($email);
            }
        }
        return render_template("login_template", ['form_errors' => $form_errors, 'email' => $email]);
    }
}
