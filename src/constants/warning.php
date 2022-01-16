<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.16 ###
##############################

namespace app\constants;

/**
 * Warning messages in html forms.
 */
class Warning
{
    // article edit warnings
    const ARTICLE_ADD_EMPTY = "Il faut donner un nom à l'article à ajouter.";
    const ARTICLE_NAME_TOO_SHORT = "Le nom de l'article doit compter au moins %s caractères.";
    const ARTICLE_NAME_TOO_LONG = "Le nom de l'article ne doit pas dépasser %s caractères.";
    const COMMENTS_NAME_TOO_LONG = "Les commentaires ne doivent pas dépasser %s caractèrs.";

    const DATE_EMPTY = "Il est nécessaire d'entrer la date d'expiration.";
    const DATE_PAST = "La date fournie doit être dans le future.";
    const DATE_INVALID = "La date fournie est invalide.";
    const DATE_FUTURE = "La date fournie est trop loin dans le future.";

    // local presets warnings
    const LOCATION_PRESET_EXISTS = "Cet emplacement est déjà présent dans la liste.";

    //login warnings
    const LOGIN_EMAIL_EMPTY = 'Une adresse e-mail valide est nécessaire.';
    const LOGIN_PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
    const LOGIN_EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
    const LOGIN_NOT_FOUND = "Il n'existe pas d'usager employant cette adresse e-mail.";
    const LOGIN_INVALID_PASSWORD = "Le mot de passe n'est pas correct.";

    //profile warnings
    const ALIAS_TOO_SHORT = "Votre alias doit au moins mesurer %s caractères.";
    const ALIAS_ALREADY_EXISTS = "Un autre utilisateur utilise déjà cet alias.";
    const DELAYS_NONE = "Il est nécessaire de cocher au moins une option.";
    const PASSWORD_DIFFERENT = "Ce mot de passe n'est pas identique au précédent.";
    const PASSWORD_REPEAT_NULL = "Il vous faut répéter votre mot de passe";

    // user edit warnings
    const USER_EMAIL_EMPTY = 'Un e-mail est nécessaire pour créer un utilisateur.';
    const USER_EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
    const USER_EMAIL_USED = 'Cette adresse e-mail est déjà utilisée par un autre utilisateur.';

    // util warnings
    const LOCATION_EMPTY = "Il est nécessaire de préciser l'emplacement.";
    const LOCATION_TOO_LONG = "Un emplacement ne peut dépasser %s caractères.";
    const LOCATION_TOO_SHORT = "Un emplacement doit au moins comporter %s caractères";
    const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
    const PASSWORD_SHORT = 'Le mot de passe doit avoir au minimum %s caractères.';
    const PASSWORD_WEAK = 'Le mot de passe doit comporter des chiffres et des lettres.';
}
