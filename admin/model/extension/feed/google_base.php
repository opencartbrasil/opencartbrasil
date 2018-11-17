<?php
class ModelExtensionFeedGoogleBase extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "google_base_category` (
				`google_base_category_id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				PRIMARY KEY (`google_base_category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "google_base_category_to_category` (
				`google_base_category_id` INT(11) NOT NULL,
				`category_id` INT(11) NOT NULL,
				PRIMARY KEY (`google_base_category_id`, `category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "google_base_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "google_base_category_to_category`");
	}

	public function import($string) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "google_base_category");

		$lines = explode("\n", $string);

		foreach ($lines as $line) {
			if (substr($line, 0, 1) != '#') {
				$part = explode(' - ', $line, 2);

				if (isset($part[1])) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "google_base_category SET google_base_category_id = '" . (int)$part[0] . "', name = '" . $this->db->escape($part[1]) . "'");
				}
			}
		}
	}

	public function getGoogleBaseCategories($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "google_base_category` WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%' ORDER BY name ASC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCategory($category_id) {
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c1.category_id = '" . (int)$category_id . "'";

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addCategory($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "google_base_category_to_category WHERE category_id = '" . (int)$data['category_id'] . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "google_base_category_to_category SET google_base_category_id = '" . (int)$data['google_base_category_id'] . "', category_id = '" . (int)$data['category_id'] . "'");
	}

	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "google_base_category_to_category WHERE category_id = '" . (int)$category_id . "'");
	}

	public function getCategories($data = array()) {
		$sql = "SELECT google_base_category_id, (SELECT name FROM `" . DB_PREFIX . "google_base_category` gbc WHERE gbc.google_base_category_id = gbc2c.google_base_category_id) AS google_base_category, category_id FROM `" . DB_PREFIX . "google_base_category_to_category` gbc2c ORDER BY google_base_category ASC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "google_base_category_to_category`");

		return $query->row['total'];
	}
}
