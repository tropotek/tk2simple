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
        $config = \Tk\Config::getInstance();

        // Include any config overriding settings
        include($config->getSrcPath() . '/App/config/config.php');

        \Tk\Url::$BASE_URL = $config->getSiteUrl();

        // * Logger [use error_log()]
        ini_set('error_log', $config->getSystemLogPath());

        \Tk\ErrorHandler::getInstance($config->getLog());
        
        // * Database init
        try {
            $pdo = \Tk\Db\Pdo::createInstance($config->getDbName(), $config->getDbUser(), $config->getDbPass(), $config->getDbHost(), $config->getDbType(), $config->getGroup('db', true));
//            $pdo->setOnLogListener(function ($entry) {
//                error_log('[' . round($entry['time'], 4) . 'sec] ' . $entry['query']);
//            });
            $config->setDb($pdo);

        } catch (\Exception $e) {
            error_log('<p>' . $e->getMessage() . '</p>');
            exit;
        }
        

        // Return if using cli (Command Line)
        if ($config->isCli()) {
            return $config;
        }

        // * Request
        $request = new \Tk\Request();
        $config->setRequest($request);
        
        // * Cookie
        $cookie = new \Tk\Cookie($config->getSiteUrl());
        $config->setCookie($cookie);
        
        // * Session
        $session = new \Tk\Session($config, $request);
        //$session->start(new \Tk\Session\Adapter\Database( $config->getDb() ));
        $session->start();
        $config->setSession($session);
        
        
        // * Dom Node Modifier
        $dm = new \Dom\Modifier\Modifier();
        $dm->add(new \Dom\Modifier\Filter\Path($config->getSiteUrl()));
        $dm->add(new \Dom\Modifier\Filter\JsLast());
        $config['dom.modifier'] = $dm;

        // * Setup the Template loader, create adapters to look for templates as needed
        /** @var \Dom\Loader $tl */
        $dl = \Dom\Loader::getInstance()->setParams($config);
        $dl->addAdapter(new \Dom\Loader\Adapter\DefaultLoader());
        $dl->addAdapter(new \Dom\Loader\Adapter\ClassPath($config->getSitePath().'/xml'));
        $config['dom.loader'] = $dl;

        return $config;
    }

}

// called by autoloader, see composer.json -> "autoload" : files [].....
Bootstrap::execute();

