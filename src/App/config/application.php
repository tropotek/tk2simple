<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
$config = \Tk\Config::getInstance();

/**
 * Config the session using PHP option names prepended with 'session.'
 * @see http://php.net/session.configuration
 */
//$config['session.save_path'] = '';
//$config['session.gc_maxlifetime'] = '1440';
//$config['session.gc_divisor'] = '100';
//$config['session.gc_probability'] = '1';

// Setup some basic admin page security
$config['system.auth.username'] = 'admin';
$config['system.auth.password'] = 'password';

/**
 * DateTime Form input format
 * @link http://php.net/manual/en/datetime.createfromformat.php
 */
$config['system.date.format.php'] = 'd/m/Y';
$config['system.date.format.js'] = 'dd/mm/yyyy';
// vd(\DateTime::createFromFormat('d/m/Y', '24/12/2012'));







/**
 * logger Helper function
 * Replacement for var_dump();
 * @todo Loock for a more appropriate place for this function????
 *
 * @return string
 */
function vd() {
    $args = func_get_args();
    $trace = debug_backtrace();
    $str = '';
    if (count($args)) {
        $str = basename($trace[0]['file']) . '[' . $trace[0]['line'] . ']: ' . "\n";
        foreach ($args as $v) {
            $str .= print_r($v, true) . "\n";
            //\Tk\Config::getInstance()->getLog()->debug(basename($trace[0]['file']) . '[' . $trace[0]['line'] . ']: ' . print_r($v, true));
        }
        //\Tk\Config::getInstance()->getLog()->debug($str);
        error_log($str);
    }
    return $str;
}

