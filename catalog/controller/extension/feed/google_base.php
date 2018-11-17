<?php
class ControllerExtensionFeedGoogleBase extends Controller {
	public function index() {
		if ($this->config->get('feed_google_base_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
			$output .= '  <channel>';
			$output .= '  <title>' . $this->config->get('config_name') . '</title>';
			$output .= '  <description>' . $this->config->get('config_meta_description') . '</description>';
			$output .= '  <link>' . $this->config->get('config_url') . '</link>';

			$this->load->model('extension/feed/google_base');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');

			$this->load->model('tool/image');

			$product_data = array();

			$google_base_categories = $this->model_extension_feed_google_base->getCategories();

			foreach ($google_base_categories as $google_base_category) {
				$filter_data = array(
					'filter_category_id' => $google_base_category['category_id'],
					'filter_filter'      => false
				);

				$products = $this->model_catalog_product->getProducts($filter_data);

				foreach ($products as $product) {
					if (!in_array($product['product_id'], $product_data) && $product['description']) {
						$product_data[] = $product['product_id'];

						$output .= '<item>';
						$output .= '<g:id>' . $product['product_id'] . '</g:id>';
						$output .= '<title><![CDATA[' . $product['name'] . ']]></title>';
						$output .= '<description><![CDATA[' . strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')) . ']]></description>';
						$output .= '<link>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</link>';

						if ($product['image']) {
							$output .= '  <g:image_link>' . $this->model_tool_image->resize($product['image'], 500, 500) . '</g:image_link>';
						} else {
							$output .= '  <g:image_link></g:image_link>';
						}

						$additional_images = $this->model_catalog_product->getProductImages($product['product_id']);
						if (!empty($additional_images)) {
							$additional_image_link = '';

							foreach ($additional_images as $additional_image) {
								$additional_image_link .= $this->model_tool_image->resize($additional_image['image'], 500, 500) . ',';
							}

							if (!empty($additional_image_link)) {
								$output .= '  <g:additional_image_link>' . rtrim($additional_image_link, ",") . '</g:additional_image_link>';
							}
						}

						$output .= '  <g:availability>' . ($product['quantity'] ? 'in stock' : 'out of stock') . '</g:availability>';

						$currency_code = $this->config->get('config_currency');
						$currency_value = $this->currency->getValue($currency_code);

						$output .= '<g:currency>' . $currency_code . '</g:currency>';

						$output .= '<g:price>' . number_format($this->tax->calculate($product['price'], $product['tax_class_id'])*$currency_value, 2, '.', '') . ' ' . $currency_code . '</g:price>';

						if ((float)$product['special']) {
							$output .= '<g:sale_price>' . number_format($this->tax->calculate($product['special'], $product['tax_class_id'])*$currency_value, 2, '.', '') . ' ' . $currency_code . '</g:sale_price>';
						}

						$output .= '  <g:google_product_category>' . $google_base_category['google_base_category_id'] . '</g:google_product_category>';

						$product_type = '';

						$path = $this->getPath($google_base_category['category_id']);
						if ($path) {
							foreach (explode('_', $path) as $path_id) {
								$category_info = $this->model_catalog_category->getCategory($path_id);

								if ($category_info) {
									if (!$product_type) {
										$product_type = $category_info['name'];
									} else {
										$product_type .= ' > ' . $category_info['name'];
									}
								}
							}
						}

						if (!empty($product_type)) {
							$output .= '<g:product_type><![CDATA[' . $product_type . ']]></g:product_type>';
						}

						if ($product['manufacturer']) {
							$output .= '<g:brand><![CDATA[' . html_entity_decode($product['manufacturer'], ENT_QUOTES, 'UTF-8') . ']]></g:brand>';
						}

						if (!empty($product['upc'])) {
							$output .= '  <g:gtin>' . $product['upc'] . '</g:gtin>';
						} else if (!empty($product['ean'])) {
							$output .= '  <g:gtin>' . $product['ean'] . '</g:gtin>';
						} else if (!empty($product['jan'])) {
							$output .= '  <g:gtin>' . $product['jan'] . '</g:gtin>';
						} else if (!empty($product['isbn'])) {
							$output .= '  <g:gtin>' . $product['isbn'] . '</g:gtin>';
						} else if (!empty($product['mpn'])) {
							$output .= '  <g:mpn><![CDATA[' . $product['mpn'] . ']]></g:mpn>' ;
						} else {
							$output .= '  <g:identifier_exists>false</g:identifier_exists>';
						}

						$output .= '<g:condition>new</g:condition>';

						if ($product['weight'] > 0) {
							$weight_units = array(
								'lb',
								'oz',
								'g',
								'kg'
							);

							$weight_class_id = $this->config->get('config_weight_class_id');
							$weight_unit = $this->weight->getUnit($weight_class_id);

							if (in_array($weight_unit, $weight_units)) {
								$output .= '  <g:shipping_weight>' . $this->weight->convert($product['weight'], $product['weight_class_id'], $weight_class_id) . ' ' . $weight_unit . '</g:shipping_weight>';
							}
						}

						if ($product['length'] > 0 && $product['width'] > 0 && $product['height'] > 0) {
							$length_units = array(
								'in',
								'cm'
							);

							$length_class_id = $this->config->get('config_length_class_id');
							$length_unit = $this->length->getUnit($length_class_id);

							if (in_array($length_unit, $length_units)) {
								$output .= '  <g:shipping_length>' . $this->length->convert($product['length'], $product['length_class_id'], $length_class_id) . ' ' . $length_unit . '</g:shipping_length>';
								$output .= '  <g:shipping_width>' . $this->length->convert($product['width'], $product['length_class_id'], $length_class_id) . ' ' . $length_unit . '</g:shipping_width>';
								$output .= '  <g:shipping_height>' . $this->length->convert($product['height'], $product['length_class_id'], $length_class_id) . ' ' . $length_unit . '</g:shipping_height>';
							}
						}

						$output .= '</item>';
					}
				}
			}

			$output .= '  </channel>';
			$output .= '</rss>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	protected function getPath($parent_id, $current_path = '') {
		$category_info = $this->model_catalog_category->getCategory($parent_id);

		if ($category_info) {
			if (!$current_path) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $current_path;
			}

			$path = $this->getPath($category_info['parent_id'], $new_path);

			if ($path) {
				return $path;
			} else {
				return $new_path;
			}
		}
	}
}
