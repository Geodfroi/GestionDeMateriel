<?php

################################
## Joël Piguet - 2021.12.14 ###
##############################

namespace app\routes;

use app\constants\AppPaths;
use app\constants\Settings;
use app\helpers\Logging;
use app\helpers\Util;

/**
 * Abstract class of all routes containing common utility functions.
 */
abstract class BaseRoute
{
    private string $redirectUri = "";
    private $alert = [];
    private $warnings = [];

    private $template_name;
    private $show_header;
    private $show_footer;

    /**
     * Link a Route with its designated template in the constructor.
     * 
     * @param string $template_name Template to use in renderTemplate call.
     * @param string $route Route's name to store in SESSION global variable (used to highlight current page in nav bar).
     */
    public function __construct(string $template_name, string $route, bool $show_header = true, bool $show_footer = true)
    {
        $_SESSION['route'] = $route;
        $this->template_name = $template_name;
        $this->show_header = $show_header;
        $this->show_footer = $show_footer;
    }

    public function getAlert()
    {
        return $this->alert ?? [];
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
        // log post and get request if debug is active
        if (Settings::DEBUG_MODE) {
            if (count($_GET) > 0) {
                Logging::debug('GET', $_GET);
            }
            if (count($_POST) > 0) {
                Logging::debug('Post', $_POST);
            }
        }

        $data['warnings'] = $this->warnings ?? [];
        return Util::renderTemplate($this->template_name, $data, AppPaths::TEMPLATES_PATH);
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
     * Set an popup alert message to be displayed.
     * 
     * @param string $type Alert type which defines alert colour; use const defined in AlertType class.
     * @param string $msg Alert message to be displayed.
     */
    public function showAlert(string $type, string $msg)
    {
        if (Settings::DEBUG_MODE) {
            Logging::debug('alert', [$type => $msg]);
        }

        $this->alert = [
            'type' => $type,
            'msg' => $msg,
        ];
    }

    /**
     * Set a warning to be displayed beneath invalid input in forms.
     */
    public function showWarning(string $key, string $content)
    {
        if (Settings::DEBUG_MODE) {
            Logging::debug('warning', [$key => $content]);
        }

        $this->warnings[$key] = $content;
    }

    /**
     * Display html footer.
     */
    public function showFooter(): bool
    {
        return $this->show_footer;
    }
    /**
     * Display html header. 
     */
    public function showHeader(): bool
    {
        return $this->show_header;
    }
}
