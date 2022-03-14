<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.14 ###
##############################
/**
 * This route handle app fetch requests for data from javascript.
 */

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Mailing;
use app\helpers\Util;
use app\helpers\Validation;
use app\models\Article;
use app\models\User;


require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug('server data', $_SERVER);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo handleGetRequests();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo handlePostRequests();
} else {
    Logging::error('Invalid request call');
    echo 'Invalid request call';
}

/**
 * Note: Get requests use Util::requestRedirect for redirection (use header php function to signal browser)
 */
function handleGetRequests(): string
{
    Logging::debug("GET request to server", ['args' => $_GET]);

    if (isset($_GET['deletearticle'])) {
        $id = intval($_GET['deletearticle']);
        return deleteArticle($id);
    }

    if (isset($_GET['deletelocpreset'])) {
        $id = intval($_GET['deletelocpreset']);
        return deleteLocationPreset($id);
    }

    if (isset($_GET['deleteuser'])) {
        $id = intval($_GET['deleteuser']);
        return deleteUser($id);
    }

    if (isset($_GET['logout'])) {
        return logout();
    }

    if (isset($_GET['renewuserpassword'])) {
        $id = intval($_GET['renewuserpassword']);
        return renewUserPassword($id);
    }

    if (isset($_GET['forgottenpassword'])) {
        $email = trim($_GET['forgottenpassword']);
        return renewForgottenPassword($email);
    }
    Logging::error("Invalid get request to server", ['args' => $_GET]);
    return "Invalid get request to server";
}

/**
 * Note: Post requests use redirect for redirection (signal javascript to redirect)
 */
function handlePostRequests(): string
{
    // Takes raw data from the request
    $json = file_get_contents('php://input');
    // Converts it into a PHP object
    $data = json_decode($json, true);

    if (!isset($data['req'])) {
        $response = ['error' => '[req] key was not defined in fetch request.'];
        Logging::error('data request error', $response);
        return json_encode($response);
    }

    Logging::debug("Post request to server", ['data' => $data['req']]);

    switch ($data['req']) {
        case 'add-article':
            return addArticle($data);
        case 'add-loc-preset':
            return addLocationPreset($data);
        case 'add-user':
            return addNewUser($data);
        case 'get-article':
            $article = Database::articles()->queryById(intval($data['id']));
            return json_encode($article->asArray());
        case 'get-user':
            return Authenticate::getUser()->toJSON();
        case 'regen-password':
            return regenPassword();
        case "submit-login":
            return submitLogin($data);
        case 'update-article':
            return updateArticle($data);
        case 'update-alias':
            return updateAlias($data);
        case 'update-delay':
            return updateDelays($data);
        case 'update-loc-preset':
            return updateLocationPreset($data);
        case 'update-password':
            return updatePassword($data);
        case "update-contact-email":
            return updateContactEmail($data);
        case 'validate-user':
            return validateNewUser($data);
        default:
            $response = [
                'error' => '[req] key was not found in fetch request.',
                'req' => $data['req']
            ];
            Logging::error('data request error', $response);
            return json_encode($response);
    }
    return json_encode($data);
}

/**
 * Send a json response containing invalid form warnings.
 * 
 * @param array $original request data sent back to javascript.
 * @param array $warnings. Warnings associative array with input field as key.
 * @return string json response.
 */
function issueWarnings(array $json, array $warnings): string
{
    $json['validated'] = false;
    $json['warnings'] = $warnings;

    return json_encode($json);
}

/**
 * Instruct js fetch function to redirect to url.
 * 
 * @param string $url The redirection path. Use the constants in Routes class to avoir typing mistakes.
 * @param string $alert_type Optional alert type. Use AlertType const.
 * @param string $alert_msg Optional alert message to be displayed after redirection.
 * @return string json response.
 */
function redirect(string $url, string $alert_type = "", string $alert_msg = ""): string
{
    if (strlen($alert_type) != 0 && strlen($alert_msg) != 0) {
        Util::storeAlert($url, $alert_type, $alert_msg);
    }
    Logging::debug('redirect_url: ' . $url);
    return json_encode(['url' => $url]);
}

#region GET requests

function deleteArticle($id): string
{
    if (Database::articles()->delete($id)) {
        $user_id = Authenticate::getUserId();
        Logging::info(LogInfo::ARTICLE_DELETED, ['user-id' => $user_id, 'article-id' => $id]);
        return Util::requestRedirect(ROUTE::ART_TABLE, AlertType::SUCCESS, Alert::ARTICLE_REMOVE_SUCCESS);
    }
    return Util::requestRedirect(ROUTE::ART_TABLE, AlertType::FAILURE, Alert::ARTICLE_REMOVE_FAILURE);
}

function deleteLocationPreset($id): string
{
    $former_location = Database::locations()->queryById($id);
    if (Database::locations()->delete($id)) {

        $user_id = Authenticate::getUserId();
        Logging::info(LogInfo::LOCATION_DELETED, [
            'user-id' => $user_id,
            'former-value' => $former_location
        ]);
        return Util::requestRedirect(ROUTE::LOCAL_PRESETS, AlertType::SUCCESS, Alert::LOC_PRESET_REMOVE_SUCCESS);
    }
    return  Util::requestRedirect(ROUTE::LOCAL_PRESETS, AlertType::FAILURE, Alert::LOC_PRESET_REMOVE_FAILURE);
}

function deleteUser($id): string
{
    if (Database::users()->delete($id)) {

        Logging::info(LogInfo::USER_DELETED, [
            'admin-id' => Authenticate::getUserId(),
            'user-id' => $id
        ]);
        return Util::requestRedirect(Route::USERS_TABLE, AlertType::SUCCESS, Alert::USER_REMOVE_SUCCESS);
    }
    return Util::requestRedirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::USER_REMOVE_FAILURE);
}

function logout(): string
{
    Authenticate::logout();
    return Util::requestRedirect(Route::LOGIN);
}

/**
 * Called by admin on user-table to re-issue user a new password.
 */
function renewUserPassword($id): string
{
    $user = Database::users()->queryById(intval($id));
    if (Util::renewPassword($user)) {
        return Util::requestRedirect(Route::USERS_TABLE, AlertType::SUCCESS, sprintf(Alert::NEW_PASSWORD_SUCCESS, $user->getLoginEmail()));
    }
    return Util::requestRedirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::NEW_PASSWORD_FAILURE);
}

/**
 * Called from login screen when the user can't log-in.
 */
function renewForgottenPassword($login_email): string
{
    // handle demand for new password.
    $user = Database::users()->queryByEmail($login_email);

    if (isset($user)) {
        if (Util::renewPassword($user)) {
            return Util::requestRedirect(
                Route::LOGIN,
                AlertType::SUCCESS,
                sprintf(Alert::NEW_PASSWORD_SUCCESS, $user->getLoginEmail())
            );
        }
    }
    return Util::requestRedirect(
        Route::LOGIN,
        AlertType::FAILURE,
        Alert::NEW_PASSWORD_FAILURE
    );
}

#endregion

#region POST requests

function addLocationPreset($json): string
{
    $content = isset($json['content']) ? $json['content'] : "";
    $warnings = [];

    if ($content_warning = Validation::validateLocationPreset($content)) {
        $warnings['content'] = $content_warning;
        return issueWarnings($json, $warnings);
    }

    if (Database::locations()->insert($content)) {

        Logging::info(LogInfo::LOCATION_CREATED, [
            'user-id' => Authenticate::getUserId(),
            'content' => $content
        ]);
        return  redirect(Route::LOCAL_PRESETS);
    }
    return redirect(Route::LOCAL_PRESETS, AlertType::FAILURE, Alert::LOCATION_PRESET_INSERT);
}

function addArticle($json): string
{
    $article_name = isset($json['article-name']) ? $json['article-name'] : "";
    $location = isset($json['location']) ? $json['location'] : "";
    $exp_date_str = isset($json['expiration-date']) ? $json['expiration-date'] : "";
    $comments = isset($json['comments']) ? $json['comments'] : "";
    $warnings = [];

    if (validateArticleInputs($article_name, $location, $exp_date_str, $comments, $warnings)) {
        $user_id = Authenticate::getUserId();
        $article = Article::fromForm($user_id, $article_name, $location, $exp_date_str, $comments);

        $article_id = Database::articles()->insert($article);
        if ($article_id) {
            Logging::info(LogInfo::ARTICLE_CREATED, ['user-id' => $user_id, 'article-id' => $article_id]);
            return redirect(Route::ART_TABLE, AlertType::SUCCESS, ALERT::ARTICLE_ADD_SUCCESS);
        }
        return redirect(Route::ART_TABLE, AlertType::FAILURE, ALERT::ARTICLE_ADD_FAILURE);
    }
    return issueWarnings($json, $warnings);
}

function addNewUser($json): string
{
    $login_email = isset($json['login-email']) ? $json['login-email'] : "";
    $password_plain = isset($json['password']) ? $json['password'] : "";
    $is_admin = isset($json['is-admin']) ? $json['is-admin'] : "";

    $new_user = User::fromForm($login_email, $password_plain, $is_admin);
    $id = Database::users()->insert($new_user);
    if ($id) {
        if (Mailing::userInviteNotification($new_user, $password_plain)) {
            Logging::info(LogInfo::USER_CREATED, [
                'admin-id' => Authenticate::getUserId(),
                'new-user' => $login_email
            ]);
            return redirect(Route::USERS_TABLE, AlertType::SUCCESS, Alert::USER_ADD_SUCCESS);
        }
        //attempt to roll back adding new user to db.
        Database::users()->delete($id);
    }
    return redirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::USER_ADD_FAILURE);
}

function regenPassword(): string
{
    return json_encode(['password' => Util::getRandomPassword()]);
}

function submitLogin($json): string
{
    $login = isset($json['login']) ? $json['login'] : "";
    $password = isset($json['password']) ? $json['password'] : "";
    $json['display_renew_btn'] = false;
    $warnings = [];

    if ($login  === '') {
        $warnings['login'] =  Warning::LOGIN_EMPTY;
        $warnings['password'] =  Warning::LOGIN_EMPTY;
        return issueWarnings($json, $warnings);
    }

    $email = filter_var($login, FILTER_VALIDATE_EMAIL);
    $user = null;

    if ($email) {
        $user = Database::users()->queryByEmail($email);
        if (!$user) {
            $warnings['login'] = Warning::LOGIN_EMAIL_NOT_FOUND;
        }
    } else {
        $user = Database::users()->queryByAlias($login);
        if (!$user) {
            $warnings['login'] = Warning::LOGIN_ALIAS_NOT_FOUND;
        }
    }

    if ($password === '') {
        $warnings['password'] = Warning::LOGIN_PASSWORD_EMPTY;
    } else {
        if ($user) {
            $json['display_renew'] = true;
            if ($user->verifyPassword($password)) {
                Authenticate::login($user);
                return redirect(Route::HOME);
            } else {
                $warnings['password'] = Warning::LOGIN_INVALID_PASSWORD;
            }
        }
    }

    return issueWarnings($json, $warnings);
}

function updateAlias($json): string
{
    $user  = Authenticate::getUser();
    if (!$user) {
        return redirect(Route::HOME);
    }

    $user_id = $user->getId();
    $alias = isset($json["alias"]) ? $json["alias"] : "";

    // alias was not actually changed
    if ($alias === $user->getAlias()) {
        return redirect(Route::PROFILE);
    }

    //validate alias
    if (strlen($alias) > 0 && strlen($alias) < USER_ALIAS_MIN_LENGHT) {
        return issueWarnings($json, ['alias' => sprintf(Warning::ALIAS_TOO_SHORT, USER_ALIAS_MIN_LENGHT)]);
    }
    $alias_arg = $alias ? $alias : $user->getLoginEmail();
    if ($existing_user = Database::users()->queryByAlias($alias_arg)) {
        if ($existing_user->getId() !== $user_id) {
            // alias already exists and assigned to another user.
            return issueWarnings($json, ['alias' => Warning::ALIAS_ALREADY_EXISTS]);
        }
    }

    if (Database::users()->updateAlias($user_id, $alias_arg)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-alias' => $alias_arg
        ]);

        if ($alias) {
            return redirect(Route::PROFILE, AlertType::SUCCESS, Alert::ALIAS_UPDATE_SUCCESS);
        }
        return redirect(Route::PROFILE, AlertType::SUCCESS, Alert::ALIAS_DELETE_SUCCESS);
    }
    return redirect(Route::PROFILE, AlertType::FAILURE, Alert::ALIAS_UPDATE_FAILURE);
}

function updateArticle($json): string
{
    $article_id  = intval($json['id']);
    $article_name = isset($json['article-name']) ? $json['article-name'] : "";
    $location = isset($json['location']) ? $json['location'] : "";
    $exp_date_str = isset($json['expiration-date']) ? $json['expiration-date'] : "";
    $comments = isset($json['comments']) ? $json['comments'] : "";
    $warnings = [];

    if (validateArticleInputs($article_name, $location, $exp_date_str, $comments, $warnings)) {
        $user_id = Authenticate::getUserId();
        $article = Database::articles()->queryById($article_id);
        $article->updateFields($article_name, $location, $exp_date_str, $comments);

        if (Database::articles()->update($article)) {
            Logging::info(LogInfo::ARTICLE_UPDATED, ['user-id' => $user_id, 'article-id' => $article_id]);
            return redirect(Route::ART_TABLE, AlertType::SUCCESS, ALERT::ARTICLE_UPDATE_SUCCESS);
        }
        return redirect(Route::ART_TABLE, AlertType::FAILURE, ALERT::ARTICLE_UPDATE_FAILURE);
    }
    Logging::debug('warnings-update', ['warnings' => $warnings]);
    return issueWarnings($json, $warnings);
}

function updateDelays($json): string
{
    $user  = Authenticate::getUser();
    if (!$user) {
        return redirect(Route::HOME);
    }

    $user_id = $user->getId();
    $delay = isset($json["delay"]) ?  $json["delay"] : "";

    if (!$delay) {
        Logging::debug('warning', ['delay' => Warning::DELAYS_NONE]);
        return issueWarnings($json, ['delay' => Warning::DELAYS_NONE]);
    }

    if (Database::users()->updateContactDelay($user_id, $delay)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-contact-delays' => $delay
        ]);

        return redirect(Route::PROFILE, AlertType::SUCCESS, Alert::DELAY_SET_SUCCESS);
    }
    return redirect(
        Route::PROFILE,
        AlertType::FAILURE,
        Alert::DELAY_SET_FAILURE
    );
}

function updateContactEmail($json): string
{
    $user  = Authenticate::getUser();
    if (!$user) {
        return redirect(Route::HOME);
    }

    $user_id = $user->getId();
    $contact_email = isset($json["contact-email"]) ? $json["contact-email"] : "";

    if ($warning = Validation::validateContactEmail($contact_email)) {
        return issueWarnings($json, ['contact-email' => $warning]);
    }

    if ($contact_email === $user->getLoginEmail()) {
        $contact_email  = '';
    }

    if (Database::users()->updateContactEmail($user_id, $contact_email)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-contact-email' => $contact_email
        ]);

        // if contact is null or empty, then contact is the login email.
        if (strlen($contact_email) > 0) {
            return redirect(Route::PROFILE, AlertType::SUCCESS, sprintf(Alert::CONTACT_SET_SUCCESS, $contact_email));
        }
        return redirect(
            Route::PROFILE,
            AlertType::SUCCESS,
            sprintf(Alert::CONTACT_RESET_SUCCESS, $user->getLoginEmail())
        );
    }
    return redirect(
        Route::PROFILE,
        AlertType::FAILURE,
        Alert::CONTACT_SET_FAILURE
    );
}

function updateLocationPreset($json): string
{
    $id = intval($json['id']);
    $content = isset($json['content']) ? $json['content'] : "";
    $warnings = [];

    $former_content = Database::locations()->queryById($id)->getContent();

    // no change to content
    if (strcasecmp($content, $former_content) == 0) {
        return redirect(Route::LOCAL_PRESETS);
    }

    // strcasecmp must be placed before, otherwise validation will always be invalid and show LOCATION_PRESET_EXISTS warning.
    if ($content_warning = Validation::validateLocationPreset($content)) {
        $warnings['content'] = $content_warning;
        return issueWarnings($json, $warnings);
    }

    if (Database::locations()->update($id, $content)) {

        Logging::info(LogInfo::LOCATION_UPDATED, [
            'user-id' => Authenticate::getUserId(),
            'former-value' => $former_content,
            'new-value' => $content
        ]);
        return redirect(Route::LOCAL_PRESETS, AlertType::SUCCESS, Alert::LOC_PRESET_UPDATE_SUCCESS);
    }
    return redirect(Route::LOCAL_PRESETS, AlertType::FAILURE, Alert::LOC_PRESET_UPDATE_FAILURE);
}

function updatePassword($json): string
{
    $user = Authenticate::getUser();
    if (!$user) {
        return redirect(Route::HOME);
    }
    $user_id = $user->getId();
    $password_plain = isset($json["password"]) ? $json["password"] : "";
    $password_repeat = isset($json["password-repeat"]) ? $json["password-repeat"] : "";
    $warnings = [];

    $password_warning = Validation::validateNewPassword($password_plain);
    $password_warning_repeat = Validation::validateNewPasswordRepeat($password_plain, $password_repeat);

    if ($password_warning) {
        $warnings['password'] = $password_warning;
    }
    if ($password_warning_repeat) {
        $warnings['password-repeat'] = $password_warning_repeat;
    }

    if ($password_warning || $password_warning_repeat) {
        return issueWarnings($json, $warnings);
    }

    $encrypted = util::encryptPassword($password_plain);

    if (Database::users()->updatePassword($user_id, $encrypted)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-password' => '*********'
        ]);
        return redirect(Route::PROFILE, AlertType::SUCCESS, Alert::PASSWORD_UPDATE_SUCCESS);
    }
    return redirect(Route::PROFILE, AlertType::FAILURE, Alert::PASSWORD_UPDATE_FAILURE);
}

/**
 * Validate form inputs before using it to add/update article.
 * 
 * @param array &$string $article_name Article name by reference.
 * @param string &$location Article's location within the school by reference.
 * @param string &$exp_date Expiration date.
 * @param string &$comments Comments to be attached to the reminder by reference.
 * @param array $warnings Array filled with warning messages if validation is unsuccessfull by reference.
 * @return bool True if validation is successful.
 */
function validateArticleInputs(string &$article_name, string &$location, string &$exp_date, string &$comments, array &$warnings): bool
{
    Logging::debug('validateArticleInputs');
    if ($article_warning = Validation::validateArticleName($article_name)) {
        $warnings['article-name'] = $article_warning;
    }
    Logging::debug('$warnings', ['w' => $warnings]);
    if ($location_warning = Validation::validateLocation($location)) {
        $warnings['location']  = $location_warning;
    }
    Logging::debug('$warnings', ['w' => $warnings]);
    if ($exp_warning = Validation::validateExpirationDate($exp_date)) {
        $warnings['expiration-date']  = $exp_warning;
    }
    Logging::debug('$warnings', ['w' => $warnings]);
    if ($comments_warning = Validation::validateComments($comments)) {
        $warnings['comments']  = $comments_warning;
    }

    return count($warnings) == 0;
}

function validateNewUser($json): string
{
    $login_email = isset($json['login-email']) ? $json['login-email'] : "";
    $password_plain = isset($json['password']) ? $json['password'] : "";

    $warnings = [];

    if ($login_warning = Validation::validateNewLogin($login_email)) {
        $warnings['login-email'] = $login_warning;
    }

    if ($password_warning  = Validation::validateNewPassword($password_plain)) {
        $warnings['password'] = $password_warning;
    }
    $json['warnings'] = $warnings;
    $json['validated'] = !$login_warning && !$password_warning;

    return json_encode($json);
}

#endregion