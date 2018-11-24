<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Proxy class
*/
class Proxy {
	/**
	 *
	 *
	 * @param	string	$key
	 */
	public function __get($key) {
		return $this->{$key};
	}

	/**
	 *
	 *
	 * @param	string	$key
	 * @param	string	$value
	 */
	public function __set($key, $value) {
		$this->{$key} = $value;
	}

	public function __call($key, $args) {
		$arg_data = array();

		$args = func_get_args();

		foreach ($args as $arg) {
			if ($arg instanceof Ref) {
				$arg_data[] =& $arg->getRef();
			} else {
				$arg_data[] =& $arg;
			}
		}

		if (isset($this->{$key})) {
			return call_user_func_array($this->{$key}, $arg_data);
		} else {
			$trace = debug_backtrace();

			exit('<b>Aviso</b>: Propriedade indefinida: Proxy::' . $key . ' no arquivo <b>' . $trace[1]['file'] . '</b> na linha <b>' . $trace[1]['line'] . '</b>');
		}
	}
}
