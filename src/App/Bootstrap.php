<?php
namespace App;


/**
 * Class Bootstrap
 *
 * This should be called to setup the App lib environment
 *
 *  \App\Bootstrap::execute();
 *
 * I am using the composer.json file to auto execute this file using the following entry:
 *
 *<code>
 *   "autoload":  {
 *     "psr-0":  {
 *       "":  [
 *         "src/"
 *       ]
 *     },
 *     "files" : [
 *       "src/App/Bootstrap.php"    <-- This one
 *     ]
 *   },
 * </code>
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
        $config = \Tk\Config::getInstance();

        // Include any config overriding settings
        include($config->getSrcPath() . '/App/config/config.php');

        // * Logger [use error_log()]
        ini_set('error_log', $config->getSystemLogPath());

        // * Database init
        try {
            $pdo = \Tk\Db\Pdo::createInstance($config->getDbName(), $config->getDbUser(), $config->getDbPass(), $config->getDbHost(), $config->getDbType());
            $pdo->setOnLogListener(function ($entry) {
                error_log('[' . round($entry['time'], 4) . 'sec] ' . $entry['query']);
            });
            $config[\Tk\Db\Pdo::CONFIG_DB] = $pdo;

        } catch (\Exception $e) {
            echo '<p>' . $e->getMessage() . '</p>';
            //$logger->addError($e->getMessage());
            exit;
        }

        // Return if using cli (Command Line)
        if ($config->isCli()) {
            return $config;
        }

        // * Session
        session_start();
        $config['session'] = $_SESSION;

        // * Request
        $config['request'] = $_REQUEST;

        // * Authentication object
        //$config['auth'] = new \Tk\Auth\Auth(new \Tk\Auth\Storage\SessionStorage($session));

        // * Dom Node Modifier
        $dm = new \Tk\Dom\Modifier\Modifier();
        $dm->add(new \Tk\Dom\Modifier\Filter\Path($config->getAppUrl()));
        $dm->add(new \Tk\Dom\Modifier\Filter\JsLast());
        $config['dom.modifier'] = $dm;

        // * Setup the Template loader, create adapters to look for templates as needed
        /** @var \Dom\Loader $tl */
        $dl = \Dom\Loader::getInstance()->setParams($config);
        $dl->addAdapter(new \Dom\Loader\Adapter\DefaultLoader());
        $dl->addAdapter(new \Dom\Loader\Adapter\ClassPath($config->getAppPath().'/xml'));
        $config['dom.loader'] = $dl;

        return $config;
    }

}

// called by autoloader, see composer.json -> "autoload" : files [].....
Bootstrap::execute();

