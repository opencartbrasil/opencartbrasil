<?php
/*
 * Ferramenta de linha de comando para instalação do projeto opencartbrasil
 *
 * Uso:
 *
 * cd install
 * php cli_install.php install --db_driver mysqli
 *                             --db_hostname localhost
 *                             --db_username root
 *                             --db_password senha
 *                             --db_database opencartbrasil
 *                             --db_port 3306
 *                             --db_prefix oc_
 *                             --username admin
 *                             --password admin
 *                             --email usuario@dominio.com.br
 *                             --http_server http://localhost/opencartbrasil
 */

ini_set('display_errors', 1);

error_reporting(E_ALL);

// DIR
define('DIR_OPENCART', str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')) . '/');
define('DIR_APPLICATION', DIR_OPENCART . 'install/');
define('DIR_SYSTEM', DIR_OPENCART . '/system/');
define('DIR_IMAGE', DIR_OPENCART . '/image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_SYSTEM . 'storage/cache/');
define('DIR_DOWNLOAD', DIR_SYSTEM . 'storage/download/');
define('DIR_LOGS', DIR_SYSTEM . 'storage/logs/');
define('DIR_MODIFICATION', DIR_SYSTEM . 'storage/modification/');
define('DIR_SESSION', DIR_SYSTEM . 'storage/session/');
define('DIR_UPLOAD', DIR_SYSTEM . 'storage/upload/');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

set_error_handler(function ($code, $message, $file, $line, array $errcontext) {
	// error was suppressed with the @-operator
	if (error_reporting() === 0) {
		return false;
	}

	throw new ErrorException($message, 0, $code, $file, $line);
});

function usage() {
	echo "Uso:\n";
	echo "======\n";
	echo "\n";
	$options = implode(" ", array(
		'--db_driver', 'mysqli',
		'--db_hostname', 'localhost',
		'--db_username', 'root',
		'--db_password', 'senha',
		'--db_database', 'opencartbrasil',
		'--db_port', '3306',
		'--db_prefix', 'oc_',
		'--username', 'admin',
		'--password', 'admin',
		'--email', 'usuario@dominio.com.br',
		'--http_server', 'http://localhost/opencartbrasil/'
	));
	echo 'php cli_install.php install ' . $options . "\n\n";
}

function get_options($argv) {
	$defaults = array(
		'db_driver'   => 'mysqli',
		'db_hostname' => 'localhost',
		'db_password' => '',
		'db_port'     => '3306',
		'db_prefix'   => 'oc_',
		'username'    => 'admin'
	);

	$options = array();
	$total = count($argv);
	for ($i=0; $i < $total; $i=$i+2) {
		$is_flag = preg_match('/^--(.*)$/', $argv[$i], $match);

		if (!$is_flag) {
			throw new Exception('O argumento ' . $argv[$i] . ' não é válido, pois argumentos devem começar com \'--\'');
		}

		$options[$match[1]] = $argv[$i+1];
	}

	return array_merge($defaults, $options);
}

function valid($options) {
	$required = array(
		'db_driver',
		'db_hostname',
		'db_username',
		'db_password',
		'db_database',
		'db_port',
		'db_prefix',
		'username',
		'password',
		'email',
		'http_server',
	);

	$missing = array();
	foreach ($required as $r) {
		if (!array_key_exists($r, $options)) {
			$missing[] = $r;
		}
	}

	if (!preg_match('#/$#', $options['http_server'])) {
		$options['http_server'] = $options['http_server'] . '/';
	}

	$valid = count($missing) === 0;

	return array($valid, $missing);
}

function install($options) {
	$check = check_requirements();
	if ($check[0]) {
		setup_db($options);
		write_config_files($options);
		dir_permissions();
	} else {
		echo 'Erro: Falha na pré-instalação: ' . $check[1] . "\n\n";
		exit(1);
	}
}

function check_requirements() {
	$error = null;
	if (version_compare(phpversion(), '5.6', '<')) {
		$error = 'Atenção: Você precisa utilizar o PHP 5.6 ou superior para o projeto OpenCart Brasil funcionar!';
	}

	if (!ini_get('file_uploads')) {
		$error = 'Atenção: file_uploads precisa ser ativado nas configurações do PHP!';
	}

	if (ini_get('session.auto_start')) {
		$error = 'Atenção: O projeto OpenCart Brasil não funcionará com session.auto_start ativado!';
	}

	if (!extension_loaded('mysqli')) {
		$error = 'Atenção: A extensão MySQLi precisa ser carregada para o OpenCart Brasil funcionar!';
	}

	if (!extension_loaded('gd')) {
		$error = 'Atenção: A extensão GD precisa ser carregada para o OpenCart Brasil funcionar!';
	}

	if (!extension_loaded('curl')) {
		$error = 'Atenção: A extensão CURL precisa ser carregada para o OpenCart Brasil funcionar!';
	}

	if (!function_exists('openssl_encrypt')) {
		$error = 'Atenção: A extensão OpenSSL precisa ser carregada para o OpenCart Brasil funcionar!';
	}

	if (!extension_loaded('zlib')) {
		$error = 'Atenção: A extensão ZLIB precisa ser carregada para o OpenCart Brasil funcionar!';
	}

	return array($error === null, $error);
}

function setup_db($data) {
	$db = new DB($data['db_driver'], htmlspecialchars_decode($data['db_hostname']), htmlspecialchars_decode($data['db_username']), htmlspecialchars_decode($data['db_password']), htmlspecialchars_decode($data['db_database']), $data['db_port']);

	$file = DIR_APPLICATION . 'opencart.sql';

	if (!file_exists($file)) {
		exit('Não foi possível carregar o arquivo sql: ' . $file);
	}

	$lines = file($file);

	if ($lines) {
		$sql = '';

		foreach ($lines as $line) {
			if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
				$sql .= $line;

				if (preg_match('/;\s*$/', $line)) {
					$sql = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . $data['db_prefix'], $sql);
					$sql = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . $data['db_prefix'], $sql);
					$sql = str_replace("INSERT INTO `oc_", "INSERT INTO `" . $data['db_prefix'], $sql);

					$db->query($sql);

					$sql = '';
				}
			}
		}

		$db->query("SET CHARACTER SET utf8");
		$db->query("DELETE FROM `" . $data['db_prefix'] . "user` WHERE user_id = '1'");
		$db->query("INSERT INTO `" . $data['db_prefix'] . "user` SET user_id = '1', user_group_id = '1', username = '" . $db->escape($data['username']) . "', salt = '" . $db->escape($salt = token(9)) . "', password = '" . $db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = 'Fulano', lastname = 'de Tal', email = '" . $db->escape($data['email']) . "', status = '1', date_added = NOW()");
		$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_email'");
		$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_email', value = '" . $db->escape($data['email']) . "'");
		$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_encryption'");
		$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_encryption', value = '" . $db->escape(token(1024)) . "'");
		$db->query("UPDATE `" . $data['db_prefix'] . "product` SET `viewed` = '0'");
		$db->query("INSERT INTO `" . $data['db_prefix'] . "api` SET username = 'Default', `key` = '" . $db->escape(token(256)) . "', status = 1, date_added = NOW(), date_modified = NOW()");
		$api_id = $db->getLastId();
		$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_api_id'");
		$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_api_id', value = '" . (int)$api_id . "'");
		$db->query("UPDATE `" . $data['db_prefix'] . "setting` SET `value` = 'FAT-" . date('Y') . "-' WHERE `key` = 'config_invoice_prefix'");
	}
}

function write_config_files($options) {
	$output  = '<?php' . "\n";
	$output .= '// HTTP' . "\n";
	$output .= 'define(\'HTTP_SERVER\', \'' . $options['http_server'] . '\');' . "\n\n";

	$output .= '// HTTPS' . "\n";
	$output .= 'define(\'HTTPS_SERVER\', \'' . $options['http_server'] . '\');' . "\n\n";

	$output .= '// DIR' . "\n";
	$output .= 'define(\'DIR_APPLICATION\', \'' . addslashes(DIR_OPENCART) . 'catalog/\');' . "\n";
	$output .= 'define(\'DIR_SYSTEM\', \'' . addslashes(DIR_OPENCART) . 'system/\');' . "\n";
	$output .= 'define(\'DIR_IMAGE\', \'' . addslashes(DIR_OPENCART) . 'image/\');' . "\n";
	$output .= 'define(\'DIR_STORAGE\', DIR_SYSTEM . \'storage/\');' . "\n";
	$output .= 'define(\'DIR_LANGUAGE\', DIR_APPLICATION . \'language/\');' . "\n";
	$output .= 'define(\'DIR_TEMPLATE\', DIR_APPLICATION . \'view/theme/\');' . "\n";
	$output .= 'define(\'DIR_CONFIG\', DIR_SYSTEM . \'config/\');' . "\n";
	$output .= 'define(\'DIR_CACHE\', DIR_STORAGE . \'cache/\');' . "\n";
	$output .= 'define(\'DIR_DOWNLOAD\', DIR_STORAGE . \'download/\');' . "\n";
	$output .= 'define(\'DIR_LOGS\', DIR_STORAGE . \'logs/\');' . "\n";
	$output .= 'define(\'DIR_MODIFICATION\', DIR_STORAGE . \'modification/\');' . "\n";
	$output .= 'define(\'DIR_SESSION\', DIR_STORAGE . \'session/\');' . "\n";
	$output .= 'define(\'DIR_UPLOAD\', DIR_STORAGE . \'upload/\');' . "\n\n";

	$output .= '// DB' . "\n";
	$output .= 'define(\'DB_DRIVER\', \'' . addslashes($options['db_driver']) . '\');' . "\n";
	$output .= 'define(\'DB_HOSTNAME\', \'' . addslashes($options['db_hostname']) . '\');' . "\n";
	$output .= 'define(\'DB_USERNAME\', \'' . addslashes($options['db_username']) . '\');' . "\n";
	$output .= 'define(\'DB_PASSWORD\', \'' . addslashes($options['db_password']) . '\');' . "\n";
	$output .= 'define(\'DB_DATABASE\', \'' . addslashes($options['db_database']) . '\');' . "\n";
	$output .= 'define(\'DB_PORT\', \'' . addslashes($options['db_port']) . '\');' . "\n";
	$output .= 'define(\'DB_PREFIX\', \'' . addslashes($options['db_prefix']) . '\');' . "\n\n";

	$file = fopen(DIR_OPENCART . 'config.php', 'w');

	fwrite($file, $output);

	fclose($file);

	$output  = '<?php' . "\n";
	$output .= '// HTTP' . "\n";
	$output .= 'define(\'HTTP_SERVER\', \'' . $options['http_server'] . 'admin/\');' . "\n";
	$output .= 'define(\'HTTP_CATALOG\', \'' . $options['http_server'] . '\');' . "\n\n";

	$output .= '// HTTPS' . "\n";
	$output .= 'define(\'HTTPS_SERVER\', \'' . $options['http_server'] . 'admin/\');' . "\n";
	$output .= 'define(\'HTTPS_CATALOG\', \'' . $options['http_server'] . '\');' . "\n\n";

	$output .= '// DIR' . "\n";
	$output .= 'define(\'DIR_APPLICATION\', \'' . addslashes(DIR_OPENCART) . 'admin/\');' . "\n";
	$output .= 'define(\'DIR_SYSTEM\', \'' . addslashes(DIR_OPENCART) . 'system/\');' . "\n";
	$output .= 'define(\'DIR_IMAGE\', \'' . addslashes(DIR_OPENCART) . 'image/\');' . "\n";	
	$output .= 'define(\'DIR_STORAGE\', DIR_SYSTEM . \'storage/\');' . "\n";
	$output .= 'define(\'DIR_CATALOG\', \'' . addslashes(DIR_OPENCART) . 'catalog/\');' . "\n";
	$output .= 'define(\'DIR_LANGUAGE\', DIR_APPLICATION . \'language/\');' . "\n";
	$output .= 'define(\'DIR_TEMPLATE\', DIR_APPLICATION . \'view/template/\');' . "\n";
	$output .= 'define(\'DIR_CONFIG\', DIR_SYSTEM . \'config/\');' . "\n";
	$output .= 'define(\'DIR_CACHE\', DIR_STORAGE . \'cache/\');' . "\n";
	$output .= 'define(\'DIR_DOWNLOAD\', DIR_STORAGE . \'download/\');' . "\n";
	$output .= 'define(\'DIR_LOGS\', DIR_STORAGE . \'logs/\');' . "\n";
	$output .= 'define(\'DIR_MODIFICATION\', DIR_STORAGE . \'modification/\');' . "\n";
	$output .= 'define(\'DIR_SESSION\', DIR_STORAGE . \'session/\');' . "\n";
	$output .= 'define(\'DIR_UPLOAD\', DIR_STORAGE . \'upload/\');' . "\n\n";

	$output .= '// DB' . "\n";
	$output .= 'define(\'DB_DRIVER\', \'' . addslashes($options['db_driver']) . '\');' . "\n";
	$output .= 'define(\'DB_HOSTNAME\', \'' . addslashes($options['db_hostname']) . '\');' . "\n";
	$output .= 'define(\'DB_USERNAME\', \'' . addslashes($options['db_username']) . '\');' . "\n";
	$output .= 'define(\'DB_PASSWORD\', \'' . addslashes($options['db_password']) . '\');' . "\n";
	$output .= 'define(\'DB_DATABASE\', \'' . addslashes($options['db_database']) . '\');' . "\n";
	$output .= 'define(\'DB_PORT\', \'' . addslashes($options['db_port']) . '\');' . "\n";
	$output .= 'define(\'DB_PREFIX\', \'' . addslashes($options['db_prefix']) . '\');' . "\n\n";

	$output .= '// OpenCart API' . "\n";
	$output .= 'define(\'OPENCART_SERVER\', \'https://www.opencart.com/\');' . "\n";

	$file = fopen(DIR_OPENCART . 'admin/config.php', 'w');

	fwrite($file, $output);

	fclose($file);
}

function dir_permissions() {
	if (stripos(PHP_OS, 'linux') === 0) {
		$dirs = array(
			DIR_OPENCART . 'image/',
			DIR_OPENCART . 'system/storage/download/',
			DIR_OPENCART . 'system/storage/upload/',
			DIR_OPENCART . 'system/storage/cache/',
			DIR_OPENCART . 'system/storage/logs/',
			DIR_OPENCART . 'system/storage/modification/',
		);

		exec('chmod o+w -R ' . implode(' ', $dirs));
	}
}

$argv = $_SERVER['argv'];
$script = array_shift($argv);
$subcommand = array_shift($argv);

switch ($subcommand) {

case "install":
	try {
		$options = get_options($argv);
		define('HTTP_OPENCART', $options['http_server']);
		$valid = valid($options);
		if (!$valid[0]) {
			echo "Erro: As seguintes entradas estão ausentes ou são inválidas: ";
			echo implode(', ', $valid[1]) . "\n\n";
			exit(1);
		}
		install($options);
		echo "\n### INSTALAÇÃO CONCLUÍDA! ###\n\n";
		echo "O projeto OpenCart Brasil foi instalado em seu servidor\n\n";
		echo "- IMPORTANTE:\n\n";
		echo "Lembre-se de remover a pasta install por segurança\n\n";
		echo "- URL DE ACESSO:\n\n";
		echo "Loja: " . $options['http_server'] . "\n\n";
		echo "Administração: " . $options['http_server'] . "admin/\n\n";
	} catch (ErrorException $e) {
		echo 'Erro: ' . $e->getMessage() . "\n";
		exit(1);
	}
	break;
case "usage":
default:
	echo usage();
}
