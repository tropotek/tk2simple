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
$config[\Tk\Db\Pdo::CONFIG_DB . '.type'] = 'mysql';
$config[\Tk\Db\Pdo::CONFIG_DB . '.host'] = 'localhost';
$config[\Tk\Db\Pdo::CONFIG_DB . '.name'] = 'dev_tk2base';
$config[\Tk\Db\Pdo::CONFIG_DB . '.user'] = 'dev';
$config[\Tk\Db\Pdo::CONFIG_DB . '.pass'] = 'dev007';


// Debug settings
$config['debug'] = true;
if ($config['debug']) {
    $config['system.log.path'] = '/home/' . trim(`whoami`) . '/log/error.log';
    $config['system.log.level'] = 'debug';
}
