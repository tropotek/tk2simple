<?php
namespace App;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Config extends \Tk\Config
{

    /**
     * getRequest
     *
     * @return \Tk\Request
     */
    public function getRequest()
    {
        if (!parent::getRequest()) {
            $obj = \Tk\Request::createFromGlobals();
            //$obj->setAttribute('config', $this);
            parent::setRequest($obj);
        }
        return parent::getRequest();
    }

    /**
     * getCookie
     *
     * @return \Tk\Cookie
     */
    public function getCookie()
    {
        if (!parent::getCookie()) {
            $obj = new \Tk\Cookie($this->getSiteUrl());
            parent::setCookie($obj);
        }
        return parent::getCookie();
    }

    /**
     * getSession
     *
     * @return \Tk\Session
     */
    public function getSession()
    {
        if (!parent::getSession()) {
            $adapter = $this->getSessionAdapter();
            $obj = \Tk\Session::getInstance($adapter, $this, $this->getRequest(), $this->getCookie());
            parent::setSession($obj);
        }
        return parent::getSession();
    }

    /**
     * getSessionAdapter
     *
     * @return \Tk\Session\Adapter\Iface|null
     */
    public function getSessionAdapter()
    {
        if (!$this->get('session.adapter')) {
            //$adapter = null;
            $adapter = new \Tk\Session\Adapter\Database($this->getDb(), new \Tk\Encrypt());
            $this->set('session.adapter', $adapter);
        }
        return $this->get('session.adapter');
    }



    /**
     * Ways to get the db after calling this method
     *
     *  - \Uni\Config::getInstance()->getDb()       //
     *  - \Tk\Db\Pdo::getInstance()                //
     *
     * Note: If you are creating a base lib then the DB really should be sent in via a param or method.
     *
     * @param string $name
     * @return mixed|\Tk\Db\Pdo
     */
    public function getDb($name = 'db')
    {
        if (!$this->get('db') && $this->has($name.'.type')) {
            try {
                $pdo = \Tk\Db\Pdo::getInstance($name, $this->getGroup($name, true));
                $this->set('db', $pdo);
            } catch (\Exception $e) {
                error_log('<p>Config::getDb(): ' . $e->getMessage() . '</p>');
                exit;
            }
        }
        return $this->get('db');
    }

    /**
     * get a dom Modifier object
     *
     * @return \Dom\Modifier\Modifier
     * @throws \Tk\Exception
     */
    public function getDomModifier()
    {
        if (!$this->get('dom.modifier')) {
            $dm = new \Dom\Modifier\Modifier();
            $dm->add(new \Dom\Modifier\Filter\UrlPath($this->getSiteUrl()));
            $dm->add(new \Dom\Modifier\Filter\JsLast());
            if (class_exists('Dom\Modifier\Filter\Less')) {
                $less = $dm->add(new \Dom\Modifier\Filter\Less($this->getSitePath(), $this->getSiteUrl(), $this->getCachePath(),
                    array('siteUrl' => $this->getSiteUrl(), 'dataUrl' => $this->getDataUrl(), 'templateUrl' => $this->getTemplateUrl())));
                $less->setCompress(true);
            }
            if ($this->isDebug()) {
                $dm->add($this->getDomFilterPageBytes());
            }
            $this->set('dom.modifier', $dm);
        }
        return $this->get('dom.modifier');
    }

    /**
     * @return \Dom\Modifier\Filter\PageBytes
     */
    public function getDomFilterPageBytes()
    {
        if (!$this->get('dom.filter.page.bytes')) {
            $obj = new \Dom\Modifier\Filter\PageBytes($this->getSitePath());
            $this->set('dom.filter.page.bytes', $obj);
        }
        return $this->get('dom.filter.page.bytes');
    }

    /**
     * getDomLoader
     *
     * @return \Dom\Loader
     */
    public function getDomLoader()
    {
        if (!$this->get('dom.loader')) {
            $dl = \Dom\Loader::getInstance()->setParams($this->all());
            $dl->addAdapter(new \Dom\Loader\Adapter\DefaultLoader());
            $this->set('dom.loader', $dl);
        }
        return $this->get('dom.loader');
    }

    /**
     * getAuth
     *
     * @return \Tk\Auth
     */
    public function getAuth()
    {
        if (!$this->get('auth')) {
            $obj = new \Tk\Auth(new \Tk\Auth\Storage\SessionStorage($this->getSession()));
            $this->set('auth', $obj);
        }
        return $this->get('auth');
    }

    /**
     * getEmailGateway
     *
     * @return \Tk\Mail\Gateway
     */
    public function getEmailGateway()
    {
        if (!$this->get('email.gateway')) {
            $gateway = new \Tk\Mail\Gateway($this);
            $gateway->setDispatcher($this->getEventDispatcher());
            $this->set('email.gateway', $gateway);
        }
        return $this->get('email.gateway');
    }


    //  -----------------------  Create methods  -----------------------


    /**
     * Helper Method
     * Make a default HTML template to create HTML emails
     * usage:
     *  $message->setBody($message->createHtmlTemplate($bodyStr));
     *
     * @param string $body
     * @param bool $showFooter
     * @return string
     * @todo: Probably not the best place for this..... Dependant on the App
     */
    public static function createMailTemplate($body, $showFooter = true)
    {
        $request = self::getInstance()->getRequest();
        $foot = '';
        if (!self::getInstance()->isCli() && $showFooter) {
            $foot .= sprintf('<i>Page:</i> <a href="%s">%s</a><br/>', $request->getUri()->toString(), $request->getUri()->toString());
            if ($request->getReferer()) {
                $foot .= sprintf('<i>Referer:</i> <span>%s</span><br/>', $request->getReferer()->toString());
            }
            $foot .= sprintf('<i>IP Address:</i> <span>%s</span><br/>', $request->getIp());
            $foot .= sprintf('<i>User Agent:</i> <span>%s</span>', $request->getUserAgent());
        }

        $defaultHtml = sprintf('
<html>
<head>
  <title>Email</title>

<style type="text/css">
body {
  font-family: arial,sans-serif;
  font-size: 80%%;
  padding: 5px;
  background-color: #FFF;
}
table {
  font-size: 0.9em;
}
th, td {
  vertical-align: top;
}
table {

}
th {
  text-align: left;
}
td {
  padding: 4px 5px;
}
.content {
  padding: 0px 0px 0px 20px;
}
p {
  margin: 0px 0px 10px 0px;
  padding: 0px;
}
</style>
</head>
<body>
  <div class="content">%s</div>
  <p>&#160;</p>
  <hr />
  <div class="footer">
    <p>
      %s
    </p>
  </div>
</body>
</html>', $body, $foot);

        return $defaultHtml;
    }





}