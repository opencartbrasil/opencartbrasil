<?php
namespace DB;
final class MySQLi {
	private $connection;

	public function __construct($hostname, $username, $password, $database, $port = '3306') {
		try {
			$mysqli = @new \mysqli($hostname, $username, $password, $database, $port);
		} catch (\mysqli_sql_exception $e) {
			$message = $e->getMessage();
			$message = str_replace(array($hostname, $username, $password, $database, $port), '*********', $message);

			throw new \Exception($message, $e->getCode());
		}

		if (!$mysqli->connect_errno) {
			$this->connection = $mysqli;
			$this->connection->report_mode = MYSQLI_REPORT_ERROR;
			$this->connection->set_charset('utf8');
			$this->connection->query("SET SESSION sql_mode = 'NO_ZERO_IN_DATE,NO_ENGINE_SUBSTITUTION'");
		} else {
			throw new \Exception('Erro: Não foi possível conectar ao banco de dados utilizando ' . $username . '@' . $hostname . '!');
		}
	}

	public function query($sql) {
		$query = $this->connection->query($sql);

		if (!$this->connection->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->num_rows = $query->num_rows;
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				unset($data);
			}
		} else {
			throw new \Exception('Erro: ' . $this->connection->error  . '<br>Código: ' . $this->connection->errno . '<br>' . $sql);
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
		return $this->connection->real_escape_string($value);
	}

	public function countAffected() {
		return $this->connection->affected_rows;
	}

	public function getLastId() {
		return $this->connection->insert_id;
	}

	public function isConnected() {
		if ($this->connection) {
			return $this->connection->ping();
		} else {
			return false;
		}
	}

	public function __destruct() {
		if ($this->connection) {
			$this->connection->close();

			unset($this->connection);
		}
	}
}
