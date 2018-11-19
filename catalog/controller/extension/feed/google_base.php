<?php
class ControllerExtensionFeedGoogleBase extends Controller {
	public function index() {
		if ($this->config->get('feed_google_base_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
			$output .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . PHP_EOL;
			$output .= '  <channel>' . PHP_EOL;
			$output .= '    <title>' . strip_tags(html_entity_decode(utf8_substr($this->config->get('config_name'), 0, 150), ENT_QUOTES, 'UTF-8')) . '</title>' . PHP_EOL;
			$output .= '    <link>' . $this->config->get('config_url') . '</link>' . PHP_EOL;
			$output .= '    <description>' . strip_tags(html_entity_decode(utf8_substr($this->config->get('config_meta_description'), 0, 300), ENT_QUOTES, 'UTF-8')) . '</description>' . PHP_EOL;

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
						if ($product['price'] > 0) {
							$product_data[] = $product['product_id'];

							$output .= '      <item>' . PHP_EOL;
							$output .= '        <g:id>' . $product['product_id'] . '</g:id>' . PHP_EOL;
							$output .= '        <g:title><![CDATA[' . strip_tags(html_entity_decode(utf8_substr($product['name'], 0, 150), ENT_QUOTES, 'UTF-8')) . ']]></g:title>' . PHP_EOL;
							$output .= '        <g:description><![CDATA[' . strip_tags(html_entity_decode(utf8_substr($product['description'], 0, 5000), ENT_QUOTES, 'UTF-8')) . ']]></g:description>' . PHP_EOL;
							$output .= '        <g:link>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</g:link>' . PHP_EOL;

							if ($product['image']) {
								$output .= '        <g:image_link>' . $this->model_tool_image->resize($product['image'], 500, 500) . '</g:image_link>' . PHP_EOL;
							} else {
								$output .= '        <g:image_link></g:image_link>' . PHP_EOL;
							}

							$additional_images = $this->model_catalog_product->getProductImages($product['product_id']);
							if (!empty($additional_images)) {
								$additional_image_link = '';

								$i = 1;
								foreach ($additional_images as $additional_image) {
									if ($i <= 10) {
										$output .= '        <g:additional_image_link>' . $this->model_tool_image->resize($additional_image['image'], 500, 500) . '</g:additional_image_link>' . PHP_EOL;
									} else {
										break;
									}
									$i++;
								}
							}

							$output .= '        <g:condition>new</g:condition>' . PHP_EOL;
							$output .= '        <g:availability>' . ($product['quantity'] ? 'in stock' : 'out of stock') . '</g:availability>' . PHP_EOL;

							$currency_code = $this->config->get('config_currency');
							$currency_value = $this->currency->getValue($currency_code);

							$output .= '        <g:currency>' . $currency_code . '</g:currency>' . PHP_EOL;
							$output .= '        <g:price>' . number_format($this->tax->calculate($product['price'], $product['tax_class_id']) * $currency_value, 2, '.', '') . ' ' . $currency_code . '</g:price>' . PHP_EOL;

							if ((float)$product['special']) {
								$output .= '        <g:sale_price>' . number_format($this->tax->calculate($product['special'], $product['tax_class_id']) * $currency_value, 2, '.', '') . ' ' . $currency_code . '</g:sale_price>' . PHP_EOL;
							}

							$gtin = '';
							if (!empty($product['upc'])) {
								$gtin = $product['upc'];
							} else if (!empty($product['ean'])) {
								$gtin = $product['ean'];
							} else if (!empty($product['jan'])) {
								$gtin = $product['jan'];
							} else if (!empty($product['isbn'])) {
								$gtin = $product['isbn'];
							}

							if (!empty($gtin)) {
								$output .= '        <g:gtin>' . $gtin . '</g:gtin>' . PHP_EOL;
							}

							$brand = $product['manufacturer'];
							if ($brand) {
								$output .= '        <g:brand><![CDATA[' . html_entity_decode(utf8_substr($brand, 0, 70), ENT_QUOTES, 'UTF-8') . ']]></g:brand>' . PHP_EOL;
							}

							$mpn = $product['mpn'];
							if (!empty($mpn)) {
								$output .= '        <g:mpn><![CDATA[' . $mpn . ']]></g:mpn>' . PHP_EOL;
							}

							if (empty($gtin) && empty($mpn)) {
								$output .= '        <g:identifier_exists>false</g:identifier_exists>' . PHP_EOL;
							}

							$output .= '        <g:google_product_category>' . $google_base_category['google_base_category_id'] . '</g:google_product_category>' . PHP_EOL;

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
								$output .= '        <g:product_type><![CDATA[' . strip_tags(html_entity_decode($product_type, ENT_QUOTES, 'UTF-8')) . ']]></g:product_type>' . PHP_EOL;
							}

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
									$output .= '        <g:shipping_weight>' . $this->weight->convert($product['weight'], $product['weight_class_id'], $weight_class_id) . ' ' . $weight_unit . '</g:shipping_weight>' . PHP_EOL;
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
									$output .= '        <g:shipping_length>' . $this->length->convert($product['length'], $product['length_class_id'], $length_class_id) . ' ' . $length_unit . '</g:shipping_length>' . PHP_EOL;
									$output .= '        <g:shipping_width>' . $this->length->convert($product['width'], $product['length_class_id'], $length_class_id) . ' ' . $length_unit . '</g:shipping_width>' . PHP_EOL;
									$output .= '        <g:shipping_height>' . $this->length->convert($product['height'], $product['length_class_id'], $length_class_id) . ' ' . $length_unit . '</g:shipping_height>' . PHP_EOL;
								}
							}

							$output .= '      </item>' . PHP_EOL;
						}
					}
				}
			}

			$output .= '  </channel>' . PHP_EOL;
			$output .= '</rss>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	private function getPath($parent_id, $current_path = '') {
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
