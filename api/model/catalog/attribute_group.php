<?php
class ModelCatalogAttributeGroup extends Model {
	public function getAttributeGroups($data = array()) {
		$sql = '
			SELECT * FROM `' . DB_PREFIX . 'attribute_group` ag
			LEFT JOIN `' . DB_PREFIX . 'attribute_group_description` agd ON (ag.attribute_group_id = agd.attribute_group_id)
			GROUP BY ag.attribute_group_id
		';

		$sort_data = array(
			'agd.name',
			'ag.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= ' ORDER BY ' . $data['sort'];
		} else {
			$sql .= ' ORDER BY agd.name';
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= ' DESC';
		} else {
			$sql .= ' ASC';
		}

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

	public function getAttributeGroupDescriptions(int $attribute_group_id) {
		$attribute_group_data = array();

		$query = $this->db->query('
			SELECT agd.*, l.code AS language_code
			FROM `' . DB_PREFIX . 'attribute_group_description` agd
			LEFT JOIN `' . DB_PREFIX . 'language` l ON (l.language_id = agd.language_id)
			WHERE agd.attribute_group_id = "' . (int)$attribute_group_id . '"
		');

		foreach ($query->rows as $result) {
			$attribute_group_data[$result['language_code']] = $result['name'];
		}

		return $attribute_group_data;
	}

	public function getTotalAttributeGroups() {
		$query = $this->db->query('SELECT COUNT(*) AS total FROM `' . DB_PREFIX . 'attribute_group`');

		return $query->row['total'];
	}
}
