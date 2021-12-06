<?php

################################
## Joël Piguet - 2021.12.06 ###
##############################

// App constants
const APP_NAME = "HEdS Gestionnaire d'inventaire";
const APP_FULL_URL = "http://localhost:8085/";
const LAST_MODIFICATION = '06 décembre 2021';

const EMAIL_TEMPLATES_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'email_templates';
const TEMPLATES_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'page_templates';

const ALIAS_MIN_LENGHT = 6;
const ARTICLE_NAME_MIN_LENGHT = 6;
const ARTICLE_NAME_MAX_LENGTH = 40;

const ARTICLE_COMMENTS_MAX_LENGHT = 240;
const ARTICLE_DATE_FUTURE_LIMIT = '2050-01-01';

const LOCATION_MIN_LENGHT = 6;
const LOCATION_MAX_LENGHT = 30;

const TABLE_DISPLAY_COUNT = 12;

const DEFAULT_PASSWORD_LENGTH = 12;
const USER_PASSWORD_MIN_LENGTH = 8;

// emails
const EMAIL_SENDER = "HEdS: gestion d'inventaire.";
const EMAIL_SUBJECT_NEW_PASSWORD = "HEdS - Gestion d'inventaire: votre nouveau mot de passe";
const EMAIL_PEREMPTION_REMINDER = "HEdS - Gestion d'inventaire: ces articles arrivent à péremption";

// routes
const ADMIN = '/admin';
const ART_TABLE = '/articlesTable';
const ART_EDIT = '/articleEdit';
const CONTACT = '/contact';
const LOCAL_PRESETS = '/location_presets';
const LOGIN = '/login';
const LOGOUT = '/login?logout=true';
const HOME = '/';
const PROFILE = '/profile';
const USER_EDIT = '/userEdit';
const USERS_TABLE = '/usersTable';

// template names
const ADMIN_TEMPLATE = 'admin_template';
const ART_EDIT_TEMPLATE = 'article_edit_template';
const ART_TABLE_TEMPLATE = "articles_table_template";
const LOC_PRESETS_TEMPLATE = 'location_presets_template';
const LOGIN_TEMPLATE = 'login_template';
const PROFILE_TEMPLATE = 'profile_template';
const USER_EDIT_TEMPLATE = 'user_edit_template';
const USER_TABLE_TEMPLATE = 'user_table_template';


// SESSION global variables keys.
const USERS_ORDERBY = 'admin_orderby';
const USERS_PAGE = 'admin_page';
const ART_ORDERBY = 'articles_orderby';
const ART_PAGE = 'articles_page';

const ADMIN_ID = 'admin_id';
const USER_ID = 'user_id';

//Database queries
const DATE_ASC = 0;
const DATE_DESC = 1;
const LOCATION_ASC = 2;
const LOCATION_DESC = 3;
const NAME_ASC = 4;
const NAME_DESC = 5;

const CREATED_ASC = 0;
const CREATED_DESC = 1;
const EMAIL_ASC = 2;
const EMAIL_DESC = 3;
const LOGIN_ASC = 4;
const LOGIN_DESC = 5;

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

// admin alerts
const USER_ADD_FAILURE = "L'utilisateur n'a pas pu être registré.";
const USER_ADD_SUCCESS = "L'utilisateur a été ajouté avec succès.";
const USER_REMOVE_FAILURE = "L'utilisateur n'a pas pu être correctement effacé.";
const USER_REMOVE_SUCCESS = "L'utilisateur a été effacé avec succès";
const USER_UPDATE_FAILURE = "Les données de l'utilisateur n'ont pas pu être mis à jour.";
const USER_UPDATE_SUCCESS = "Les données de l'utilisateur ont été mises à jour avec succès";

// article edit errors
const ARTICLE_ADD_EMPTY = "Il faut donner un nom à l'article à ajouter.";
const ARTICLE_NAME_TOO_SHORT = "Le nom de l'article doit compter au moins %s caractères.";
const ARTICLE_NAME_TOO_LONG = "Le nom de l'article ne doit pas dépasser %s caractères.";
const COMMENTS_NAME_TOO_LONG = "Les commentaires ne doivent pas dépasser %s caractèrs.";

const DATE_EMPTY = "Il est nécessaire d'entrer la date d'expiration.";
const DATE_PAST = "La date fournie doit être dans le future.";
const DATE_INVALID = "La date fournie est invalide.";
const DATE_FUTURE = "La date fournie est trop loin dans le future.";

// article table alerts
const ARTICLE_ADD_FAILURE = "L'article n'a pas pu être enregistré.";
const ARTICLE_ADD_SUCCESS = "L'article a été ajouté avec succès.";
const ARTICLE_REMOVE_FAILURE = "L'article n'a pas pu être correctement effacé.";
const ARTICLE_REMOVE_SUCCESS = "L'article a été effacé avec succès.";
const ARTICLE_UPDATE_FAILURE = "L'article n'a pas pu être mis à jour.";
const ARTICLE_UPDATE_SUCCESS = "L'article a été mis à jour avec succès.";

// local presets alerts
const LOCATION_PRESET_INSERT = "Il n'a pas été possible d'ajouter le nouvel emplacement à la liste.";
const LOC_PRESET_REMOVE_FAILURE = "L'emplacement n' pas pu être enlevé.";
const LOC_PRESET_REMOVE_SUCCESS = "L'emplacement a été enlevé avec succès.";
const LOC_PRESET_UPDATE_SUCCESS = "L'emplacement a été modifié avec succès.";

// local presets errors
const LOCATION_PRESET_EXISTS = "Cet emplacement est déjà présent dans la liste.";

//login alerts
const LOGIN_USER_DISC = "L'usager précédent s'est déconnecté.";
const LOGIN_NEW_PASSWORD_FAILURE = "Le changement de mot de passe a échoué.";
const LOGIN_NEW_PASSWORD_SUCCESS = "Un nouveau mot de passe a été envoyé dans votre boîte email.";

//login errors
const LOGIN_EMAIL_EMPTY = 'Un e-mail est nécessaire pour vous connecter.';
const LOGIN_PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
const LOGIN_EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
const LOGIN_NOT_FOUND = "Il n'existe pas d'usager employant cette adresse e-mail.";
const LOGIN_INVALID_PASSWORD = "Le mot de passe n'est pas correct.";

//profile alerts
const ALIAS_DELETE_SUCCESS = "Vous avez effacé votre alias. Votre e-mail sera utilisé pour vous identifier auprès des autres utilisateurs.";
const ALIAS_UPDATE_FAILURE = "Votre alias n'a pas pu être modifié.";
const ALIAS_UPDATE_SUCCESS = "Votre alias a été modifié avec succès.";
const CONTACT_RESET_SUCCESS = "Vos e-mail de rappels sont désormais envoyé à [%s].";
const CONTACT_SET_FAILURE = "Le changement d'adresse de contact a échoué.";
const CONTACT_SET_SUCCESS = "Votre nouvelle adresse de contact [%s] a été définie avec succès.";
const DELAY_SET_FAILURE = "Les délais de contact avant péremption n'ont pas pu être modifié.";
const DELAY_SET_SUCCESS = "Les délais de contact avant péremption ont été modifié avec succès.";
const PASSWORD_UPDATE_FAILURE = "Le changement de mot de passe a échoué.";
const PASSWORD_UPDATE_SUCCESS = "Le mot de passe a été modifié avec succès.";

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
