<?php
namespace App;


/**
 * Class Bootstrap
 *
 * This should be called to setup the App lib environment
 *
 * ~~~php
 *     \App\Bootstrap::execute();
 * ~~~
 *
 * I am using the composer.json file to auto execute this file using the following entry:
 *
 * ~~~json
 *   "autoload":  {
 *     "psr-0":  {
 *       "":  [
 *         "src/"
 *       ]
 *     },
 *     "files" : [
 *       "src/App/Bootstrap.php"    <-- This one
 *     ]
 *   }
 * ~~~
 *
 *
 * @author Michael Mifsud <info@tropotek.com>  
 * @link http://www.tropotek.com/  
 * @license Copyright 2015 Michael Mifsud  
 */
class Bootstrap
{

    /**
     * This will also load dependant objects into the config, so this is the DI object for now.
     *
     */
    static function execute()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            // php version must be high enough to support traits
            throw new \Exception('Your PHP5 version must be greater than 5.4.0 [Curr Ver: '.phpversion().']');
        }

        // Do not call \Tk\Config::getInstance() before this point

        // If you use sub folders un your URL's you must define the paths manually
        //$sitePath = dirname(__FILE__);
        //$siteUrl = dirname($_SERVER['PHP_SELF']);
        $config = \Tk\Config::getInstance();

        // Include any config overriding settings
        include($config->getSrcPath() . '/config/config.php');

        if ($config->has('date.timezone'))
            ini_set('date.timezone', $config->get('date.timezone'));

        \Tk\Uri::$BASE_URL_PATH = $config->getSiteUrl();
        if ($config->isDebug()) {
            \Dom\Template::$enableTracer = true;
        }

        // * Logger [use error_log()]
        ini_set('error_log', $config->getSystemLogPath());
        error_log('------ Start ------');
        \Tk\ErrorHandler::getInstance($config->getLog());

        // Return if using cli (Command Line)
        if ($config->isCli()) {
            return $config;
        }


        // --- HTTP only bootstrapping from here ---

        if ($config->isDebug()) {
            error_reporting(-1);
            //error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', 'Off');       // Only log errors?????
        } else {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        }

        // * Request
        Factory::getRequest();
        // * Cookie
        Factory::getCookie();
        // * Session
        Factory::getSession();

        // Initiate the default database connection
        \App\Factory::getDb();

        // initalise Dom Loader
        \App\Factory::getDomLoader();

        // Initiate the email gateway
        \App\Factory::getEmailGateway();

        return $config;
    }

}

// called by autoloader, see composer.json -> "autoload" : files [].....
Bootstrap::execute();

