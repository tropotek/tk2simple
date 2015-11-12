<?php
namespace App;



/**
 * Class Url
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Url extends \Tk\Url
{

    /**
     * Create a url, However if a relative path to a file is given
     * the project site root is prepended to the given URL.
     *
     * When using create() or new \Tk\Url() there are three ways to supply the spec path:
     *  o http://www.example.com/path/to/index.html  =>  http://www.example.com/path/to/index.html
     *  o /path/to/index.html  =>  http://www.sitehost.com/siteUrl/path/to/index.html     <-- Convert to relative
     *  o path/to/index.html   =>  http://www.sitehost.com/siteUrl/path/to/index.html     <-- Convert to relative
     *
     * @param string $spec
     * @return \Tk\Url
     */
    public static function create($spec = '')
    {
        if ($spec instanceof \Tk\Url) {
            return $spec;
        }
        if ($spec) {
            $config = \Tk\Config::getInstance();
            //if (!preg_match('/^(#|javascript|mailto)/i', $spec) && !preg_match('/^([a-zA-Z_-]+\:\/\/)/', $spec)) {
            // make sure path is ralative: IE: not `http://domain`, `mailto:email@`..., `//domain`, etc
            // TODO: not checked `domain.com/path/path`, need to create a regex for this one day
            if (!preg_match('/^(#|javascript|mailto)/i', $spec) && !preg_match('/^([a-zA-Z_-]?\:?\/\/)/', $spec)) {
                $spec = str_replace($config->getAppUrl(), '', $spec);

                if ($spec[0] == '/') {  // prepend site URL
                    $spec = trim($spec, '/');
                    $spec = $config->getAppUrl() . '/' . $spec;
                } else {
                    // TODO: What do we do with relative paths, for now the same as absolute?????
                    $spec = trim($spec, '/');
                    $spec = $config->getAppUrl() . '/' . $spec;
                }
            }
        }

        $url = new self($spec);
        return $url;

    }

}