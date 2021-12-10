<?php

################################
## Joël Piguet - 2021.12.10 ###
##############################

namespace app\routes;

use app\constants\Settings;
use app\helpers\Util;

/**
 * Abstract class of all routes containing common utility functions.
 */
abstract class BaseRoute
{
    private string $redirectUri = "";
    private $alert = [];
    private $errors = [];

    /**
     * Link a Route with its designated template in the constructor.
     * 
     * @param string $templateName Template to use in renderTemplate call.
     * @param string $route Route's name to store in SESSION global variable (used to highlight current page in nav bar).
     */
    public function __construct(string $templateName, string $route)
    {
        $_SESSION['route'] = $route;
        $this->templateName = $templateName;
    }


    /**
     * Send a header to the browser requesting a redirection to the path provided.
     * 
     * @param string $uri The redirection path. Use the constants in Routes class to avoir typing mistakes.
     */
    protected function requestRedirect(string $uri)
    {
        $this->redirectUri = $uri;

        //The header php function will send a header message to the browser, here signaling for redirection.
        header("Location: $uri", true);
    }

    /**
     * Executes the custom php code in the route and returns html dynamically.
     * 
     * @return string HTML content to be inserted into the main template body.
     */
    public abstract function getBodyContent(): string;

    /**
     * Allow to customize a title for each path. If the function is not overloaded in a route, a default title is return instead.
     * 
     * @return string Title to be displayed in browser.
     */
    public function getHeaderTitle(): string
    {
        return Settings::APP_NAME;
    }

    /**
     * As a redirecting uri has been provided. Further rendering of this route is to be cancelled.
     */
    public function isRedirecting(): bool
    {
        return strlen($this->redirectUri) > 0;
    }

    /** 
     * Load a php template in memory and returns a content string.
     * 
     * @param array $data The variables to be used in php templates.
     */
    protected function renderTemplate(array $data = []): string
    {
        $data['alert']  = $this->alert ?? [];
        $data['errors'] = $this->errors ?? [];

        if (count($this->errors)) {
            var_dump($this->errors);
        }

        return Util::renderTemplate($this->templateName, $data, Settings::TEMPLATES_PATH);
    }

    /**
     * Set an alert to be displayed. Alerts are popups messages.
     * 
     * @param string $type Alert type which defines alert colour; use const defined in AlertType class.
     * @param string $msg Alert message to be displayed.
     */
    public function setAlert(string $type, string $msg)
    {
        $this->alert = [
            'type' => $type,
            'msg' => $msg,
        ];
    }

    /**
     * Set an error to be displayed. Errors are displayed beneath invalid input in forms.
     */
    public function setError(string $key, string $content)
    {
        $this->errors[$key] = $content;
    }
}
