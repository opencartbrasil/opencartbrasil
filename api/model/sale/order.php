<?php

class ModelSaleOrder extends Model {

	public function getOrders($data = array()) {
		$sql = "SELECT
			*,
			CONVERT_TZ(`date_added`, @@time_zone, \"+00:00\") AS `date_added`,
			CONVERT_TZ(`date_modified`, @@time_zone, \"+00:00\") AS `date_modified`
			FROM `" . DB_PREFIX . "order` o
		";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "`order_status_id` = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE `order_status_id` > '0'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(`date_added`) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(`date_modified`) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		$sql .= " ORDER BY `order_id` DESC";

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

	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(order_id) AS total FROM `" . DB_PREFIX . "order`";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "`order_status_id` = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE `order_status_id` > '0'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(`date_added`) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(`date_modified`) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("
			SELECT
				op.*,
				p.sku,
				p.ncm,
				p.cest
			FROM `" . DB_PREFIX . "order_product` op
			LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = op.product_id)
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->rows;
	}

	public function getOptionsOrderProducts($order_product_id) {
		$query = $this->db->query("
			SELECT
				oo.name,
				oo.value,
				oov.sku
			FROM `" . DB_PREFIX . "order_option` oo
			LEFT JOIN `" . DB_PREFIX . "product_option_value` oov ON (oov.product_option_value_id = oo.product_option_value_id)
			WHERE `order_product_id` = '" . (int)$order_product_id . "'
		");

		return $query->rows;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("
			SELECT
				oh.`order_status_id`,
				oh.`date_added`,
				oh.`comment`,
				oh.`notify`
			FROM `" . DB_PREFIX . "order_history` oh
			WHERE oh.`order_id` = '" . (int)$order_id . "'
				ORDER BY oh.date_added DESC
				LIMIT " . (int)$start . "," . (int)$limit
		);

		return $query->rows;
	}
}
