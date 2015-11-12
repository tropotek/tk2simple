<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
$config = \Tk\Config::getInstance();

//Default config setup
include_once(__DIR__ . '/application.php');


// Database access
$config['db.type'] = 'mysql';
$config['db.host'] = 'localhost';
$config['db.name'] = 'dev_tk2base';
$config['db.user'] = 'dev';
$config['db.pass'] = 'dev007';


// Debug settings
$config['debug'] = true;
if ($config['debug']) {
    $config['system.log.path'] = '/home/' . trim(`whoami`) . '/log/error.log';
    $config['system.log.level'] = 'debug';
}
