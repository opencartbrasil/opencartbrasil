<?php
// Registry
$registry = new Registry();

// Config
$config = new Config();

// Load the default config
$config->load('default');
$config->load($application_config);
$registry->set('config', $config);

// Log
$log = new Log($config->get('error_filename'));
$registry->set('log', $log);

date_default_timezone_set($config->get('date_timezone'));

set_error_handler(function($code, $message, $file, $line) use($log, $config) {
	// error suppressed with @
	if (error_reporting() === 0) {
		return false;
	}

	switch ($code) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}

	if ($config->get('error_display')) {
		echo '<b>' . $error . '</b>: ' . $message . ' no arquivo <b>' . $file . '</b> na linha <b>' . $line . '</b>';
	}

	if ($config->get('error_log')) {
		$log->write('PHP ' . $error . ':  ' . $message . ' no arquivo ' . $file . ' na linha ' . $line);
	}

	return true;
});

set_exception_handler(function($e) use ($log, $config) {
	if ($config->get('error_display')) {
		echo '<b>' . get_class($e) . '</b>: ' . $e->getMessage() . ' no arquivo <b>' . $e->getFile() . '</b> na linha <b>' . $e->getLine() . '</b>';
	}

	if ($config->get('error_log')) {
		$log->write(get_class($e) . ':  ' . $e->getMessage() . ' no arquivo ' . $e->getFile() . ' na linha ' . $e->getLine());
	}
});

// Event
$event = new Event($registry);
$registry->set('event', $event);

// Event Register
if ($config->has('action_event')) {
	foreach ($config->get('action_event') as $key => $value) {
		foreach ($value as $priority => $action) {
			$event->register($key, new Action($action), $priority);
		}
	}
}

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Request
$registry->set('request', new Request());

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->addHeader('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
$response->addHeader('Pragma: no-cache');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Database
if ($config->get('db_autostart')) {
	$registry->set('db', new DB($config->get('db_engine'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port')));
}

// Session
$session = new Session($config->get('session_engine'), $registry);
$registry->set('session', $session);

if ($config->get('session_autostart')) {
	if (isset($_COOKIE[$config->get('session_name')])) {
		$session_id = $_COOKIE[$config->get('session_name')];
	} else {
		$session_id = '';
	}

	$session->start($session_id);

	setcookie($config->get('session_name'), $session->getId(), (ini_get('session.cookie_lifetime') ? (time() + ini_get('session.cookie_lifetime')) : 0), ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
}

// Cache
$registry->set('cache', new Cache($config->get('cache_engine'), $config->get('cache_expire')));

// Url
if ($config->get('url_autostart')) {
	$registry->set('url', new Url($config->get('site_url'), $config->get('site_ssl')));
}

// Language
$language = new Language($config->get('language_directory'));
$registry->set('language', $language);

// Document
$registry->set('document', new Document());

// Config Autoload
if ($config->has('config_autoload')) {
	foreach ($config->get('config_autoload') as $value) {
		$loader->config($value);
	}
}

// Language Autoload
if ($config->has('language_autoload')) {
	foreach ($config->get('language_autoload') as $value) {
		$loader->language($value);
	}
}

// Library Autoload
if ($config->has('library_autoload')) {
	foreach ($config->get('library_autoload') as $value) {
		$loader->library($value);
	}
}

// Model Autoload
if ($config->has('model_autoload')) {
	foreach ($config->get('model_autoload') as $value) {
		$loader->model($value);
	}
}

// Route
$route = new Router($registry);

// Pre Actions
if ($config->has('action_pre_action')) {
	foreach ($config->get('action_pre_action') as $value) {
		$route->addPreAction(new Action($value));
	}
}

// Dispatch
$route->dispatch(new Action($config->get('action_router')), new Action($config->get('action_error')));

// Output
$response->output();