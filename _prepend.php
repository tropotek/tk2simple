<?php

include(dirname(__FILE__) . '/vendor/autoload.php');

// If you use sub folders You must define the paths manually
$sitePath = dirname(__FILE__);
$siteUrl = dirname($_SERVER['PHP_SELF']);
$config = \Tk\Config::getInstance($sitePath, $siteUrl);





