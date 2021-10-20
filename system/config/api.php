<?php
// Site
$_['site_url']                 = HTTP_SERVER;
$_['site_ssl']                 = HTTPS_SERVER;

// Url
$_['url_autostart']            = false;

// Security
$_['api_secret_key']           = 'opencartbrasil';
$_['api_cache_expire']         = 60;
$_['api_request_per_minute']   = 30;

// Ignore middlewares in
$_['ignored_routers']          = array(
	'credentials/access_token',
	'credentials/refresh_token',
	'common/documentation',
);

// Log
$_['error_filename']           = sprintf('api-%d-%s-%s.log', date('Y'), date('m'), date('d'));

// Database
$_['db_autostart']             = true;
$_['db_engine']                = DB_DRIVER; // mysqli, pdo or pgsql
$_['db_hostname']              = DB_HOSTNAME;
$_['db_username']              = DB_USERNAME;
$_['db_password']              = DB_PASSWORD;
$_['db_database']              = DB_DATABASE;
$_['db_port']                  = DB_PORT;
$_['db_list_per_page']         = 50;

// Session
$_['session_autostart']        = true;
$_['session_engine']           = 'db';
$_['session_name']             = 'OCSESSION';

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
$_['library_autoload']         = array();

// Actions
$_['action_pre_action']        = array(
	'middlewares/response_time/before',
	'startup/startup',
	'startup/event',
	'startup/seo_url',
	'startup/login',
);

// Actions
$_['action_post_action']       = array(
	'middlewares/response_time/after',
);

// Action Events
$_['action_event']             = array(
	'controller/*/before' => array(
		'middlewares/logs/before',
		'middlewares/status/before',
		'middlewares/access_token_expired/before',
		'middlewares/check_request_method/before',
		'middlewares/ip/before',
		'middlewares/rate_limit/before',
	),
	'controller/*/after' => array(
		'middlewares/logs/after',
	)
);

// Webhook Events
$_['action_webhook']           = array(
	'model/catalog/product/add/after' => array(
		'catalog/product/add'
	),
	'model/catalog/product/update/after' => array(
		'catalog/product/edit'
	),
	'model/catalog/product/updateStock/after' => array(
		'catalog/product/edit'
	),
	'model/catalog/product/deleteProduct/after' => array(
		'catalog/product/delete'
	),
);
