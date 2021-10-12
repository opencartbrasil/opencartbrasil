<?php
class ModelCatalogFilter extends Model {
	public function getHasFilterById(array $data = array()) {
		$filters_id = array_map('intval', $data);

		$result = $this->db->query('SELECT filter_id FROM `' . DB_PREFIX . 'filter` WHERE `filter_id` IN (' . implode(',', $filters_id) . ')');

		if (count($filters_id) > $result->num_rows) {
			$categories_founds = array_map(function($item) {
				return $item['filter_id'];
			}, $result->rows);

			return array_diff($filters_id, $categories_founds);
		}

		return true;
	}

	public function getFilterGroups(array $data = array()) {
		$sql = 'SELECT fg.* FROM `' . DB_PREFIX . 'filter_group` fg';

		if (isset($data['offset']) || isset($data['limit'])) {
			if ($data['offset'] < 0) {
				$data['offset'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['offset'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getFilterGroupDescriptionById(int $filter_group_id) {
		$sql = '
			SELECT fd.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'filter_group_description` fd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.`language_id` = fd.language_id)
			WHERE fd.`filter_group_id` = ' . $filter_group_id . '
		';

		$query = $this->db->query($sql);

		$result = array();

		foreach ($query->rows as $key => $row) {
			$language_code = $row['language_code'];

			$result['name'][$language_code] = $row['name'];
		}

		return $result;
	}

	public function getFiltersByGroupId(int $filter_group_id) {
		$sql = '
			SELECT fg.*, fgd.name, l.code AS language_code
			FROM `' . DB_PREFIX . 'filter` fg
			LEFT JOIN `' . DB_PREFIX . 'filter_description` fgd ON (fgd.filter_id = fg.filter_id)
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = fgd.language_id)
			WHERE fgd.`filter_group_id` = ' . $filter_group_id . '
		';

		$query = $this->db->query($sql);

		$result = array();

		foreach ($query->rows as $row) {
			$result[$row['filter_id']]['filter_id'] = (int)$row['filter_id'];
			$result[$row['filter_id']]['sort_order'] = (int)$row['sort_order'];
			$result[$row['filter_id']]['name'][$row['language_code']] = $row['name'];
		}

		return $result;
	}

	public function getTotalFilterGroups() {
		$sql = 'SELECT COUNT(*) AS total FROM `' . DB_PREFIX . 'filter_group`';

		$query = $this->db->query($sql);

		return intval($query->row['total']);
	}
}
