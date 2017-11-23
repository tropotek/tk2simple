<?php
namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
        $config = Factory::getConfig();

        // This maybe should be created in a Factory or DI Container....
        if (is_readable($config->getLogPath())) {
            if (!\App\Factory::getRequest()->has('nolog')) {
                $logger = new Logger('system');
                $handler = new StreamHandler($config->getLogPath(), $config->getLogLevel());
                $formatter = new \Tk\Log\MonologLineFormatter();
                $formatter->setScriptTime($config->getScriptTime());
                $handler->setFormatter($formatter);
                $logger->pushHandler($handler);
                $config->setLog($logger);
                \Tk\Log::getInstance($logger);
            }
        } else {
            error_log('Log Path not readable: ' . $config->getLogPath());
        }


        if (!$config->isDebug()) {
            ini_set('display_errors', 'Off');
            error_reporting(0);
        } else {
            \Dom\Template::$enableTracer = true;
        }

        // Init framework error handler
        \Tk\ErrorHandler::getInstance($config->getLog());

        // Initiate the default database connection
        \App\Factory::getDb();
        $config->replace(\Tk\Db\Data::create()->all());


        // Return if using cli (Command Line)
        if ($config->isCli()) return $config;


        // --- HTTP only bootstrapping from here ---

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

