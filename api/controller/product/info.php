<?php
class ControllerProductInfo extends Controller {

	private const HTTP_STATUS_404 = 404;

	public function index() {
		$this->load->model('catalog/product');

		if (isset($this->request->get['product_id'])) {
			$product_id = intval($this->request->get['product_id']);
		} else {
			$product_id = null;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if (empty($product_info)) {
			return $this->response([], self::HTTP_STATUS_404);
		}

		/** Attributes */
		$product_attributes = $this->model_catalog_product->getProductAttributes($product_id);

		if ($product_attributes) {
			$product_info['attributes'] = $product_attributes;
		}

		/** Descriptions */
		$product_descriptions = $this->model_catalog_product->getProductDescriptions($product_id);

		if ($product_descriptions) {
			$product_info = array_merge($product_info, array_filter($product_descriptions));
		}

		/** Discounts */
		$product_discounts = $this->model_catalog_product->getProductDiscounts($product_id);

		if ($product_discounts) {
			$product_info['discounts'] = $product_discounts;
		}

		/** Filters */
		$product_filters = $this->model_catalog_product->getProductFilters($product_id);

		if ($product_filters) {
			$product_info['filters'] = array_map('intval', $product_filters);
		}

		/** Additional Images */
		$product_images = $this->model_catalog_product->getProductImages($product_id);

		if ($product_images) {
			foreach ($product_images as $image) {
				$product_info['additional_images'][] = $image['image'];
			}
		}

		/** Options */

		/** Recurring */
		$product_recurrings = $this->model_catalog_product->getProductRecurrings($product_id);

		if ($product_recurrings) {
			foreach ($product_recurrings as $recurring) {
				$product_info['recurring'][] = [
					'recurring_id' => intval($recurring['recurring_id']),
					'customer_group_id' => intval($recurring['customer_group_id']),
				];
			}
		}

		/** Products Related */
		$product_related = $this->model_catalog_product->getProductsRelated($product_id);

		return $this->response($product_info);
	}

	/**
	 * Exibe resposta para o cliente
	 *
	 * @param int $status
	 *
	 * @return void
	 */
	protected function response(array $data = array(), int $status = 200) {
		$this->response->addHeader('HTTP/1.1 ' . $status);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
}
