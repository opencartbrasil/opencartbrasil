<?php
// Site
$_['site_url']           = HTTP_SERVER;
$_['site_ssl']           = HTTPS_SERVER;

// Url
$_['url_autostart']      = false;

// Credentials
$_['secret_key'] 		 = 'opencartbrasil';

// Log
$_['error_filename']     = sprintf('api-%d-%s-%d.log', date('Y'), date('m'), date('d'));

// Database
$_['db_autostart']       = true;
$_['db_engine']          = DB_DRIVER; // mysqli, pdo or pgsql
$_['db_hostname']        = DB_HOSTNAME;
$_['db_username']        = DB_USERNAME;
$_['db_password']        = DB_PASSWORD;
$_['db_database']        = DB_DATABASE;
$_['db_port']            = DB_PORT;

// Session
$_['session_autostart']  = true;
$_['session_engine']     = 'db';
$_['session_name']       = 'OCSESSION';

// Template
$_['image_max_filesize']       = 2 * 1024 * 1024; // 2MB in bytes
$_['image_dir_download']       = DIR_IMAGE . 'catalog/api/';
$_['image_download_overwrite'] = false;
$_['image_mimetypes']          = [
	'image/jpeg',
	'image/pjpeg',
	'image/png',
	'image/x-png',
	'image/gif',
];

// Autoload Libraries
$_['library_autoload']   = array();

// Actions
$_['action_pre_action']  = array(
	'startup/startup',
	'startup/event',
	'startup/seo_url',
	'startup/login',
	'startup/permission',
);

// Action Events
$_['action_event']       = array(
	'controller/*/before' => array(
		'middlewares/logs/before',
		'middlewares/ip/before',
		'middlewares/response_time/before',
	),
	'controller/*/after' => array(
		'middlewares/response_time/after',
		'middlewares/logs/after',
	)
);
