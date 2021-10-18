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
 *                             --db_prefix ocbr_
 *                             --username usuario
 *                             --password senha
 *                             --email usuario@dominio
 *                             --http_server http://localhost/opencartbrasil
 */

ini_set('display_errors', 1);

error_reporting(E_ALL);

// DIR
define('DIR_APPLICATION', str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_SYSTEM', str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')) . '/system/');
define('DIR_OPENCART', str_replace('\\', '/', realpath(DIR_APPLICATION . '../')) . '/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_DATABASE', DIR_SYSTEM . 'database/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_MODIFICATION', DIR_SYSTEM . 'modification/');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

set_error_handler(function ($code, $message, $file, $line, array $errcontext) {
	// error was suppressed with the @-operator
	if (0 === error_reporting()) {
		return false;
	}

	throw new ErrorException($message, 0, $code, $file, $line);
});

function usage() {
	echo "Uso:\n";
	echo "====\n";
	echo "\n";
	$options = implode(" ", array(
		'--db_driver', '"mysqli" \\' . PHP_EOL,
		'--db_hostname', '"localhost" \\' . PHP_EOL,
		'--db_username', '"root" \\' . PHP_EOL,
		'--db_password', '"123456" \\' . PHP_EOL,
		'--db_database', '"opencartbrasil" \\' . PHP_EOL,
		'--db_port', '"3306" \\' . PHP_EOL,
		'--db_prefix', '"ocbr_" \\' . PHP_EOL,
		'--username', '"usuario" \\' . PHP_EOL,
		'--password', '"senha" \\' . PHP_EOL,
		'--email', '"usuario@dominio" \\' . PHP_EOL,
		'--http_server', '"http://localhost/opencartbrasil/"'
	));
	echo 'php cli_install.php install ' . $options . "\n\n";
}

function get_options($argv) {
	$defaults = array(
		'db_driver'   => 'mysqli',
		'db_hostname' => 'localhost',
		'db_password' => '',
		'db_port'     => '3306',
		'db_prefix'   => 'ocbr_',
		'username'    => 'usuario'
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

	$options['http_server'] = rtrim($options['http_server'], '/') . '/';

	return count($missing) > 0 ? $missing : true;
}

function install($options) {
	$check = check_requirements($options);

	if ($check === true) {
		setup_db($options);
		write_config_files($options);
		dir_permissions();
	} else {
		echo 'Erro: Falha na pré-instalação: ' . $check . "\n\n";
		exit(1);
	}
}

function check_requirements($options) {
	if (version_compare(phpversion(), '5.6', '<') || version_compare(phpversion(), '8.0', '>=')) {
		return 'Atenção: Você precisa utilizar PHP 5.6 até 7.4 para o projeto OpenCart Brasil funcionar!';
	}

	if (!ini_get('file_uploads')) {
		return 'Atenção: A opção "file_uploads" precisa ser ativada nas configurações do PHP!';
	}

	if (ini_get('session.auto_start')) {
		return 'Atenção: O projeto OpenCart Brasil não funcionará com a configuração "session.auto_start" ativada!';
	}

	$db_drivers = array(
		'mysql',
		'pdo',
		'pgsql'
	);

	if (!in_array($options['db_driver'], $db_drivers)) {
		return 'Atenção: Não há suporte para o driver "' . $options['db_driver'] . '" de banco de dados!';
	}

	if (!extension_loaded($options['db_driver'])) {
		return 'Atenção: A extensão "' . $options['db_driver'] . '" precisa estar habilitada para o OpenCart Brasil funcionar!';
	}

	if (!extension_loaded('gd')) {
		return 'Atenção: A extensão "GD" precisa estar habilitada para o OpenCart Brasil funcionar!';
	}

	if (!extension_loaded('curl')) {
		return 'Atenção: A extensão "CURL" precisa estar habilitada para o OpenCart Brasil funcionar!';
	}

	if (!function_exists('openssl_encrypt')) {
		return 'Atenção: A extensão "OpenSSL" precisa estar habilitada para o OpenCart Brasil funcionar!';
	}

	if (!extension_loaded('zlib')) {
		return 'Atenção: A extensão ZLIB precisa estar habilitada para o OpenCart Brasil funcionar!';
	}

	return true;
}

function setup_db($data) {
	try {
		$db = new DB($data['db_driver'], htmlspecialchars_decode($data['db_hostname']), htmlspecialchars_decode($data['db_username']), htmlspecialchars_decode($data['db_password']), htmlspecialchars_decode($data['db_database']), $data['db_port']);
	} catch (Exception $e) {
		echo $e->getMessage();
		exit(1);
	}

	$file = DIR_APPLICATION . 'opencart.sql';

	if (!file_exists($file)) {
		echo('O arquivo "' . $file . '" não foi encontrado.');
		exit(1);
	}

	$lines = file($file);

	if ($lines === false) {
		echo('Não foi possível carregar o arquivo sql: ' . $file);
		exit(1);
	}

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
	$output .= 'define(\'DIR_WEBHOOK\', \'' . addslashes(DIR_OPENCART) . 'webhook/\');' . "\n";
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
	$output .= 'define(\'DIR_WEBHOOK\', \'' . addslashes(DIR_OPENCART) . 'webhook/\');' . "\n";
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

	$output  = '<?php' . "\n";
	$output .= '// HTTP' . "\n";
	$output .= 'define(\'HTTP_SERVER\', \'' . $options['http_server'] . 'api/\');' . "\n";
	$output .= 'define(\'HTTP_CATALOG\', \'' . $options['http_server'] . '\');' . "\n\n";

	$output .= '// HTTPS' . "\n";
	$output .= 'define(\'HTTPS_SERVER\', \'' . $options['http_server'] . 'api/\');' . "\n";
	$output .= 'define(\'HTTPS_CATALOG\', \'' . $options['http_server'] . '\');' . "\n\n";

	$output .= '// DIR' . "\n";
	$output .= 'define(\'DIR_APPLICATION\', \'' . addslashes(DIR_OPENCART) . 'api/\');' . "\n";
	$output .= 'define(\'DIR_SYSTEM\', \'' . addslashes(DIR_OPENCART) . 'system/\');' . "\n";
	$output .= 'define(\'DIR_IMAGE\', \'' . addslashes(DIR_OPENCART) . 'image/\');' . "\n";
	$output .= 'define(\'DIR_WEBHOOK\', \'' . addslashes(DIR_OPENCART) . 'webhook/\');' . "\n";
	$output .= 'define(\'DIR_STORAGE\', DIR_SYSTEM . \'storage/\');' . "\n";
	$output .= 'define(\'DIR_LANGUAGE\', DIR_APPLICATION . \'language/\');' . "\n";
	$output .= 'define(\'DIR_TEMPLATE\', DIR_APPLICATION . \'view/template/\');' . "\n";
	$output .= 'define(\'DIR_CONFIG\', DIR_SYSTEM . \'config/\');' . "\n";
	$output .= 'define(\'DIR_CACHE\', DIR_STORAGE . \'cache/\');' . "\n";
	$output .= 'define(\'DIR_LOGS\', DIR_STORAGE . \'logs/\');' . "\n";
	$output .= 'define(\'DIR_MODIFICATION\', DIR_STORAGE . \'modification/\');' . "\n";

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

	$file = fopen(DIR_OPENCART . 'api/config.php', 'w');

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
		$valid = valid($options);

		if ($valid !== true) {
			echo "Erro: As seguintes entradas estão ausentes ou são inválidas: ";
			echo implode(', ', $valid) . "\n\n";
			exit(1);
		}

		define('HTTP_OPENCART', $options['http_server']);

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
