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
}
