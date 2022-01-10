<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.10 ###
##############################

namespace app\constants;

/**
 * Alert messages in html.
 */
class Alert
{
    // admin alerts
    const USER_ADD_FAILURE = "L'utilisateur n'a pas pu être registré.";
    const USER_ADD_SUCCESS = "L'utilisateur a été ajouté avec succès.";
    const USER_REMOVE_FAILURE = "L'utilisateur n'a pas pu être correctement effacé.";
    const USER_REMOVE_SUCCESS = "L'utilisateur a été effacé avec succès";
    const USER_UPDATE_FAILURE = "Les données de l'utilisateur n'ont pas pu être mis à jour.";
    const USER_UPDATE_SUCCESS = "Les données de l'utilisateur ont été mises à jour avec succès";

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

    // //login alerts
    // const LOGIN_USER_DISC = "L'usager précédent s'est déconnecté.";

    //profile alerts
    const ALIAS_DELETE_SUCCESS = "Vous avez effacé votre alias. Votre e-mail sera utilisé pour vous identifier auprès des autres utilisateurs.";
    const ALIAS_UPDATE_FAILURE = "Votre alias n'a pas pu être modifié.";
    const ALIAS_UPDATE_SUCCESS = "Votre alias a été modifié avec succès.";
    const ALIAS_EXISTS_FAILURE = "Un autre utilisateur utilise déjà cet alias.";
    const CONTACT_RESET_SUCCESS = "Vos e-mail de rappels sont désormais envoyé à [%s].";
    const CONTACT_SET_FAILURE = "Le changement d'adresse de contact a échoué.";
    const CONTACT_SET_SUCCESS = "Votre nouvelle adresse de contact [%s] a été définie avec succès.";
    const DELAY_SET_FAILURE = "Les délais de contact avant péremption n'ont pas pu être modifié.";
    const DELAY_SET_SUCCESS = "Les délais de contact avant péremption ont été modifié avec succès.";
    const PASSWORD_UPDATE_FAILURE = "Le changement de mot de passe a échoué.";
    const PASSWORD_UPDATE_SUCCESS = "Le mot de passe a été modifié avec succès.";
    const USER_NOT_FOUND = "L'utilisateur n'a pas pu être identifié.";

    // util alerts
    const NEW_PASSWORD_FAILURE = "Le changement de mot de passe a échoué.";
    const NEW_PASSWORD_SUCCESS = "Un nouveau mot de passe a été envoyé à [%s].";
}
