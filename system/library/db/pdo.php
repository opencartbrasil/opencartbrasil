<?php
namespace DB;
final class PDO {
	private $connection;

	public function __construct($hostname, $username, $password, $database, $port = '3306') {
		try {
			$pdo = @new \PDO("mysql:host=" . $hostname . ";port=" . $port . ";dbname=" . $database . ";charset=UTF8", $username, $password, array(\PDO::ATTR_PERSISTENT => false));
		} catch (\PDOException $e) {
			$message = $e->getMessage();
			$message = str_replace(array($hostname, $username, $password, $database, $port), '*********', $message);

			throw new \Exception($message, $e->getCode());
		}

		if ($pdo) {
			$this->connection = $pdo;
			$this->connection->query("SET SESSION sql_mode = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION'");
		}
	}

	public function query($sql, $params = array()) {
		$this->statement = $this->connection->prepare($sql);

		try {
			if ($this->statement && $this->statement->execute($params)) {
				$data = array();

				while ($row = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->row = (isset($data[0]) ? $data[0] : array());
				$result->rows = $data;
				$result->num_rows = $this->statement->rowCount();
			}
		} catch (\PDOException $e) {
			throw new \Exception('Erro: ' . $e->getMessage() . '<br>Código : ' . $e->getCode() . '<br>' . $sql);
		}

		if (!isset($result)) {
			$result = new \stdClass();
			$result->num_rows = 0;
			$result->row = array();
			$result->rows = array();
		}

		return $result;
	}

	public function escape($value) {
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

	public function countAffected() {
		if ($this->statement) {
			return $this->statement->rowCount();
		} else {
			return 0;
		}
	}

	public function getLastId() {
		return $this->connection->lastInsertId();
	}

	public function isConnected() {
		if ($this->connection) {
			return true;
		} else {
			return false;
		}
	}

	public function __destruct() {
		unset($this->connection);
	}
}
