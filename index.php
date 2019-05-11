<?php
// Version
define('VERSION', '3.0.3.2');
define('OPENCART_BRASIL', '1.3.3');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit();
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');