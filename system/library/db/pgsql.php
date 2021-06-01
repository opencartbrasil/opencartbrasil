<?php
namespace DB;
final class PgSQL {
	private $connection;

	public function __construct($hostname, $username, $password, $database, $port = '5432') {
		try {
			$pg = @pg_connect('hostname=' . $hostname . ' port=' . $port . ' username=' . $username . ' password=' . $password . ' database=' . $database);
		} catch (\Exception $e) {
			$message = $e->getMessage();
			$message = str_replace(array($hostname, $username, $password, $database, $port), '*********', $message);

			throw new \Exception($message, $e->getCode());
		}

		if ($pg) {
			$this->connection = $pg;
			pg_query($this->connection, "SET CLIENT_ENCODING TO 'UTF8'");
		}
	}

	public function query($sql) {
		$resource = pg_query($this->connection, $sql);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($row = pg_fetch_assoc($resource)) {
					$data[$i] = $row;

					$i++;
				}

				pg_free_result($resource);

				$result = new \stdClass();
				$result->num_rows = $i;
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				unset($data);
			}
		} else {
			throw new \Exception('Erro: ' . pg_result_error($this->connection) . '<br>Código: ' . $sql);
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
		return pg_escape_string($this->connection, $value);
	}

	public function countAffected() {
		return pg_affected_rows($this->connection);
	}

	public function isConnected() {
		if (pg_connection_status($this->connection) == PGSQL_CONNECTION_OK) {
			return true;
		} else {
			return false;
		}
	}

	public function getLastId() {
		$query = $this->query("SELECT LASTVAL() AS `id`");

		return $query->row['id'];
	}

	public function __destruct() {
		if ($this->isConnected()) {
			pg_close($this->connection);
		}
	}
}
