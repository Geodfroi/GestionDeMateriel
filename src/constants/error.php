<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

class Error
{
    // article edit errors
    const ARTICLE_ADD_EMPTY = "Il faut donner un nom à l'article à ajouter.";
    const ARTICLE_NAME_TOO_SHORT = "Le nom de l'article doit compter au moins %s caractères.";
    const ARTICLE_NAME_TOO_LONG = "Le nom de l'article ne doit pas dépasser %s caractères.";
    const COMMENTS_NAME_TOO_LONG = "Les commentaires ne doivent pas dépasser %s caractèrs.";

    const DATE_EMPTY = "Il est nécessaire d'entrer la date d'expiration.";
    const DATE_PAST = "La date fournie doit être dans le future.";
    const DATE_INVALID = "La date fournie est invalide.";
    const DATE_FUTURE = "La date fournie est trop loin dans le future.";

    // local presets errors
    const LOCATION_PRESET_EXISTS = "Cet emplacement est déjà présent dans la liste.";

    //login errors
    const LOGIN_EMAIL_EMPTY = 'Un e-mail est nécessaire pour vous connecter.';
    const LOGIN_PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
    const LOGIN_EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
    const LOGIN_NOT_FOUND = "Il n'existe pas d'usager employant cette adresse e-mail.";
    const LOGIN_INVALID_PASSWORD = "Le mot de passe n'est pas correct.";

    //profile errors
    const ALIAS_TOO_SHORT = "Votre alias doit au moins mesurer %s caractères.";
    const DELAYS_NONE = "Il est nécessaire de cocher au moins une option.";
    const PASSWORD_DIFFERENT = "Ce mot de passe n'est pas identique au précédent.";
    const PASSWORD_REPEAT_NULL = "Il vous faut répéter votre mot de passe";

    // user edit errors
    const USER_EMAIL_EMPTY = 'Un e-mail est nécessaire pour créer un utilisateur.';
    const USER_EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
    const USER_EMAIL_USED = 'Cet adresse e-mail est déjà utilisée par un autre utilisateur.';

    // util errors
    const LOCATION_EMPTY = "Il est nécessaire de préciser l'emplacement.";
    const LOCATION_TOO_LONG = "Un emplacement ne peut dépasser %s caractères.";
    const LOCATION_TOO_SHORT = "Un emplacement doit au moins comporter %s caractères";
    const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
    const PASSWORD_SHORT = 'Le mot de passe doit avoir au minimum %s caractères.';
    const PASSWORD_WEAK = 'Le mot de passe doit comporter des chiffres et des lettres.';

    //Database errors
    const ARTICLE_DELETE = 'failure to delete article from database: ';
    const ARTICLE_INSERT = 'failure to insert article: ';
    const ARTICLE_QUERY = 'failure to retrieve article [%s] from database: ';
    const ARTICLES_COUNT_QUERY = 'failure to count articles from database: ';
    const ARTICLES_QUERY = 'failure to retrieve article list from database: ';
    const LOCATIONS_CHECK_CONTENT = 'failure to check for content string: ';
    const LOCATION_DELETE = 'failure to delete location from database: ';
    const LOCATION_INSERT = 'failure to properly insert new location: ';
    const LOCATION_QUERY = 'failure to retrieve location [%s] from database: ';
    const LOCATIONS_QUERY_ALL = 'failure to retrieve locations: ';
    const LOCATION_UPDATE = 'failure to update location string correctly: ';
    const USER_ALIAS_UPDATE = 'failure to update user alias: ';
    const USER_ARTICLES_DELETE = 'failure to delete user articles from database: ';
    const USER_CONTACT_UPDATE = 'failure to update user contact email: ';
    const USER_DELAY_UPDATE = 'failure to update user contact delay: ';
    const USER_DELETE = 'failure to delete user from database: ';
    const USER_INSERT = 'failure to insert user: ';
    const USER_LOGTIME_UPDATE = 'failure to update user log time: ';
    const USER_PASSWORD_UPDATE = 'failure to update user password: ';
    const USER_QUERY = 'failure to retrieve user from database: ';
    const USERS_COUNT_QUERY = 'failure to count users from database: ';
    const USERS_QUERY = 'failure to retrieve user list from database: ';
}
