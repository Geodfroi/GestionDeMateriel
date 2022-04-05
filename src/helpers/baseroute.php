<?php

################################
## Joël Piguet - 2022.04.04 ###
##############################

namespace app\helpers;

use app\constants\AppPaths;
use app\constants\Session;
use app\helpers\Logging;
use app\helpers\Util;

/**
 * Abstract class of all routes containing common utility functions.
 */
abstract class BaseRoute
{
    /**
     * string
     */
    private $redirectUri = "";

    private $javascript_name;
    private $template_name;
    private $show_header;
    private $show_footer;

    /**
     * Link a Route with its designated template in the constructor.
     * 
     * @param string $route_folder Route's uri to be collated after app URI.
     * @param string $template_name Template to use in renderTemplate call. 
     * @param string $javascript_name Optional javascript file to execute in the page.
     * @param bool $show_header The page display the AppBar header.
     * @param bool $show_footer The page display the school info footer.
     */
    public function __construct(string $route_folder, string $template_name, string $javascript_name = "", bool $show_header = true, bool $show_footer = true)
    {
        // $page_url Route's uri to store in SESSION global variable (used to highlight current page in nav bar).
        $_SESSION[Session::PAGE] = APP_URL . $route_folder;
        $this->show_header = $show_header;
        $this->show_footer = $show_footer;
        $this->javascript_name = $javascript_name;
        $this->template_name = $template_name;
    }

    /**
     * Executes the custom php code in the route and returns html dynamically.
     * 
     * @return string HTML content to be inserted into the main template body.
     */
    protected abstract function getBodyContent(): string;

    /**
     * Allow to customize a title for each path. If the function is not overloaded in a route, a default title is return instead.
     * 
     * @return string Title to be displayed in browser.
     */
    protected function getHeaderTitle(): string
    {
        return APP_NAME;
    }

    /**
     * Get utility function script shared by all pages.
     * 
     * @return string Javascript text.
     */
    private static function getMainScript()
    {
        // $script_path = AppPaths::SCRIPTS . DIRECTORY_SEPARATOR . 'main.js';
        return file_get_contents(AppPaths::MAIN_SCRIPT);
    }

    // /**
    //  * Get js script to be executed on the page.
    //  * 
    //  * @return string Javascript text.
    //  */
    // protected function getScript()
    // {
    //     if (strlen($this->javascript_name) == 0)
    //         return "";

    //     $script_path = $this->javascript_name . '.js';
    //     return file_get_contents($script_path);
    // }


    /**
     * As a redirecting uri has been provided. Further rendering of this route is to be cancelled.
     */
    protected function isRedirecting(): bool
    {
        return strlen($this->redirectUri) > 0;
    }

    /** 
     * Load a php template in memory and returns a content string.
     * 
     * @param array $data Associative array to be made avaible in both php templates and javascript scripts.
     * @return string templated html
     */
    protected function renderTemplate(array $data = []): string
    {
        // log post and get request if debug is active
        if (DEBUG_MODE) {
            if (count($_GET) > 0) {
                Logging::debug('GET', $_GET);
            }
            if (count($_POST) > 0) {
                Logging::debug('Post', $_POST);
            }
        }

        return Util::renderTemplate($this->template_name, $data);
    }

    /**
     * Send a header to the browser requesting a redirection to the path provided. Optionaly display an alert after redirection.
     * 
     * @param string $uri The redirection path. Use the constants in Routes class to avoir typing mistakes.
     * @param string $alert_type Optional alert type. Use AlertType const.
     * @param string $alert_msg Optional alert message to be displayed after redirection.
     * @return string Return empty string.
     */
    protected function requestRedirect(string $uri, string $alert_type = "", $alert_msg = ""): string
    {
        $this->redirectUri = $uri;
        return Util::requestRedirect($uri, $alert_type, $alert_msg);
    }

    /**
     * Display html footer.
     */
    protected function showFooter(): bool
    {
        return $this->show_footer;
    }
    /**
     * Display html header. 
     */
    protected function showHeader(): bool
    {
        return $this->show_header;
    }

    /**
     * Each route correspond to a path (i.e '/login') and is responsible to dynamically generate customized html content. Content is rendered a first time with page specific date in $this->getBodyContent() function; then that content is rendered inside the main root-template containing the header / footer, main script and alert logic.
     */
    public function renderRoute(): String
    {
        $templateData = [];

        if (!$this->isRedirecting()) {
            $templateData['page_title'] = $this->getHeaderTitle();
            $templateData['page_content'] = $this->getBodyContent();
            $templateData['script_name'] = $this->javascript_name;
            $templateData['show_header'] = $this->showHeader();
            $templateData['show_footer'] = $this->showFooter();
            $templateData['alert'] = Util::displayAlert();
        }

        // insert dynamically generated html content into the main template.
        return Util::renderTemplate('root_template', $templateData, AppPaths::TEMPLATES);
    }
}
