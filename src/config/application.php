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
include_once(__DIR__ . '/session.php');

/**
 * Set the system timezone
 */
$config['date.timezone'] = 'Australia/Victoria';


// Setup some basic admin page security
//$config['system.auth.username'] = 'admin';
//$config['system.auth.password'] = 'password';
