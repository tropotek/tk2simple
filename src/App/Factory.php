<?php
namespace App;
use Tk\Db\Pdo;

/**
 * Class Factory
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Factory
{
    
    /**
     * Get Config object or array
     * 
     * @param string $sitePath
     * @param string $siteUrl
     * @return \Tk\Config
     */
    static public function getConfig($sitePath = '', $siteUrl = '')
    {
        return \Tk\Config::getInstance($sitePath, $siteUrl);
    }
    

    /**
     * @return \Tk\Request
     */
    static public function getRequest()
    {
        if (!self::getConfig()->getRequest()) {
            $obj = \Tk\Request::create();
            $obj->setAttribute('config', self::getConfig());;
            self::getConfig()->setRequest($obj);
        }
        return self::getConfig()->getRequest();
    }

    /**
     * @return \Tk\Cookie
     */
    static public function getCookie()
    {
        if (!self::getConfig()->getCookie()) {
            $obj = new \Tk\Cookie(self::getConfig()->getSiteUrl());
            self::getConfig()->setCookie($obj);
        }
        return self::getConfig()->getCookie();
    }

    /**
     * @return \Tk\Session
     */
    static public function getSession()
    {
        if (!self::getConfig()->getSession()) {
            $adapter = null;
            $adapter = new \Tk\Session\Adapter\Database(self::getDb(), new \Tk\Encrypt());
            $obj = new \Tk\Session($adapter, self::getConfig(), self::getRequest(), self::getCookie());
            self::getConfig()->setSession($obj);
        }
        return self::getConfig()->getSession();
    }

    /**
     * getEmailGateway
     *
     * @return \Tk\Mail\Gateway
     */
    public static function getEmailGateway()
    {
        if (!self::getConfig()->getEmailGateway()) {
            $gateway = new \Tk\Mail\Gateway(self::getConfig());
            $gateway->setDispatcher(self::getEventDispatcher());
            self::getConfig()->setEmailGateway($gateway);
        }
        return self::getConfig()->getEmailGateway();
    }

    /**
     * getDb
     * Ways to get the db after calling this method
     *
     *  - \App\Factory::getDb()                 // Application level call
     *  - \Tk\Config::getInstance()->getDb()    //
     *  - \Tk\Db\Pdo::getInstance()             //
     *
     * Note: If you are creating a base lib then the DB really should be sent in via a param or method.
     *
     * @param string $name
     * @return mixed|Pdo
     */
    static public function getDb($name = 'default')
    {
        $config = self::getConfig();
        if (!$config->getDb() && $config->has('db.type')) {
            try {
                $pdo = Pdo::getInstance($name, $config->getGroup('db'));
                $logger = $config->getLog();
//                if ($logger && $config->isDebug()) {
//                    $pdo->setOnLogListener(function ($entry) use ($logger) {
//                        $logger->debug('[' . round($entry['time'], 4) . 'sec] ' . $entry['query']);
//                    });
//                }
                $config->setDb($pdo);
            } catch (\Exception $e) {
                error_log('<p>' . $e->getMessage() . '</p>');
                exit;
            }
            self::getConfig()->setDb($pdo);
        }
        return self::getConfig()->getDb();
    }
    
    /**
     * get a dom Modifier object
     * 
     * @return \Dom\Modifier\Modifier
     */
    static public function getDomModifier()
    {
        if (!self::getConfig()->getDomModifier()) {
            $dm = new \Dom\Modifier\Modifier();
            $config = self::getConfig();
            $dm->add(new \Dom\Modifier\Filter\UrlPath($config->getSiteUrl()));
            $dm->add(new \Dom\Modifier\Filter\JsLast());
            $dm->add(new \Dom\Modifier\Filter\Less($config->getSitePath(), $config->getSiteUrl(), $config->getCachePath(),
                array('siteUrl' => $config->getSiteUrl(), 'dataUrl' => $config->getDataUrl(), 'templateUrl' => $config->getTemplateUrl())));
            if (self::getConfig()->isDebug()) {
                $dm->add(self::getDomFilterPageBytes());
            }
            self::getConfig()->setDomModifier($dm);
        }
        return self::getConfig()->getDomModifier();
    }

    /**
     * @return \Dom\Modifier\Filter\PageBytes
     */
    public static function getDomFilterPageBytes()
    {
        if (!self::getConfig()->getDomFilterPageBytes()) {
            $obj = new \Dom\Modifier\Filter\PageBytes(self::getConfig()->getSitePath());
            self::getConfig()->setDomFilterPageBytes($obj);
        }
        return self::getConfig()->getDomFilterPageBytes();
    }

    /**
     * getDomLoader
     * 
     * @return \Dom\Loader
     */
    static public function getDomLoader()
    {   
        if (!self::getConfig()->getDomLoader()) {
            $dl = \Dom\Loader::getInstance()->setParams(self::getConfig()->all());
            $dl->addAdapter(new \Dom\Loader\Adapter\DefaultLoader());
            if (self::getConfig()->getTemplatePath()) {
                $dl->addAdapter(new \Dom\Loader\Adapter\ClassPath(self::getConfig()->getTemplatePath() . '/xtpl'));
            }
            self::getConfig()->setDomLoader($dl);
        }
        return self::getConfig()->getDomLoader();
    }

}