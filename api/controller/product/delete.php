<?php
class ControllerProductDelete extends Controller {
	private const HTTP_STATUS_404 = 404;

	public function index(int $product_id = 0) {
		$this->load->model('catalog/product');

		if (isset($this->request->get['product_id'])) {
			$product_id = intval($this->request->get['product_id']);
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if (empty($product_info)) {
			return $this->response(array(), self::HTTP_STATUS_404);
		}

		if (!preg_match('/no_image\.png$/', $product_info['image']) && file_exists(DIR_IMAGE . $product_info['image'])) {
			@unlink(DIR_IMAGE . $product_info['image']);
		}

		$product_images = $this->model_catalog_product->getProductImages($product_id);

		foreach ($product_images as $image) {
			@unlink(DIR_IMAGE . $image['image']);
		}

		$this->model_catalog_product->deleteProduct($product_id);

		header('HTTP/1.1 204');
	}

	/**
	 * Display response
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
