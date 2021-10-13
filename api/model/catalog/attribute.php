<?php
class ModelCatalogAttribute extends Model {
	public function getHasAttributeById(array $data = array()) {
		$attributes_id = array_map('intval', $data);

		$result = $this->db->query('SELECT attribute_id FROM `' . DB_PREFIX . 'product_attribute` WHERE `attribute_id` IN (' . implode(',', $attributes_id) . ')');

		if (count($attributes_id) > $result->num_rows) {
			$attributes_founds = array_map(function($item) {
				return $item['attribute_id'];
			}, $result->rows);

			return array_diff($attributes_id, $attributes_founds);
		}

		return true;
	}

	public function getAttributes($data = array()) {
		$sql = '
			SELECT *
			FROM `' . DB_PREFIX . 'attribute` a
			LEFT JOIN `' . DB_PREFIX . 'attribute_description` ad ON (a.attribute_id = ad.attribute_id)
			WHERE a.attribute_id > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_attribute_group_id']) && $data['filter_attribute_group_id'] !== '') {
			$sql .= " AND a.attribute_group_id = '" . (int)$data['filter_attribute_group_id'] . "'";
		}

		$sql .= ' GROUP BY a.attribute_id';

		$sort_data = array(
			'ad.name',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ad.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	public function getAttributeDescriptions(int $attribute_id) {
		$attribute_data = array();

		$query = $this->db->query('
			SELECT ad.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'attribute_description` ad
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = ad.language_id)
			WHERE ad.attribute_id = "' . (int)$attribute_id . '"
		');

		foreach ($query->rows as $result) {
			$attribute_data[$result['language_code']] = $result['name'];
		}

		return $attribute_data;
	}

	public function getTotalAttributes($data = array()) {
		$sql = '
			SELECT COUNT(DISTINCT a.attribute_id) AS total
			FROM `' . DB_PREFIX . 'attribute` a
			LEFT JOIN `' . DB_PREFIX . 'attribute_description` ad ON (a.attribute_id = ad.attribute_id)
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = ad.language_id)
			WHERE a.attribute_id > 0
		';

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_attribute_group_id']) && $data['filter_attribute_group_id'] !== '') {
			$sql .= " AND a.attribute_group_id = '" . (int)$data['filter_attribute_group_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
