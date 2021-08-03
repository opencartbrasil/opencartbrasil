<?php
// Version
define('VERSION', '3.0.3.3');
define('OPENCART_BRASIL', '1.6.1');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: ../install/index.php');
	exit();
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('admin');
