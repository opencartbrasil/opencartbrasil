<?php
class ModelCatalogDownload extends Model {
	public function getHasDownloadById(array $data = array()) {
		$downloads_id = array_map('intval', $data);

		$result = $this->db->query('SELECT download_id FROM `' . DB_PREFIX . 'download` WHERE `download_id` IN (' . implode(',', $downloads_id) . ')');

		if (count($downloads_id) > $result->num_rows) {
			$categories_founds = array_map(function($item) {
				return $item['download_id'];
			}, $result->rows);

			return array_diff($downloads_id, $categories_founds);
		}

		return true;
	}

	public function getDownloads(array $data = array()) {
		$sql = '
			SELECT
				d.*,
				CONVERT_TZ(d.`date_added`, @@time_zone, "+00:00") AS `date_added`
			FROM `' . DB_PREFIX . 'download` d
			LEFT JOIN `' . DB_PREFIX . 'download_description` dd ON (dd.download_id = d.download_id)
			WHERE d.download_id > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= ' AND dd.name LIKE "%' . $this->db->escape($data['filter_name']) . '%"';
		}

		$sql .= ' GROUP BY d.download_id';

		if (isset($data['offset']) || isset($data['limit'])) {
			if ($data['offset'] < 0) {
				$data['offset'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= ' LIMIT ' . (int)$data['offset'] . ',' . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getDownloadDescriptions(int $download_id) {
		$attribute_data = array();

		$query = $this->db->query('
			SELECT dd.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'download_description` dd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = dd.language_id)
			WHERE dd.download_id = "' . (int)$download_id . '"
		');

		foreach ($query->rows as $result) {
			$attribute_data[$result['language_code']] = $result['name'];
		}

		return $attribute_data;
	}

	public function getTotalDownloads(array $data = array()) {
		$sql = '
			SELECT COUNT(DISTINCT d.download_id) AS total
			FROM `' . DB_PREFIX . 'download` d
			LEFT JOIN `' . DB_PREFIX . 'download_description` dd ON (dd.download_id = d.download_id)
			WHERE d.download_id > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= ' AND dd.name LIKE "%' . $this->db->escape($data['filter_name']) . '%"';
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
