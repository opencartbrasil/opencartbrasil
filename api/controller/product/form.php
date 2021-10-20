<?php
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\Exception\LogicException;
use Swaggest\JsonSchema\InvalidValue;

class ControllerProductForm extends Controller {
	private const HTTP_STATUS_201 = 201;
	private const HTTP_STATUS_400 = 400;
	private const HTTP_STATUS_500 = 500;

	public function index() {
		$this->load->model('catalog/product');

		if (isset($this->request->get['product_id'])) {
			$product_id = intval($this->request->get['product_id']);
		} else {
			$product_id = null;
		}

		// Validate Schema
		$result = $this->validateJsonSchema($this->request->json);

		if (!$result->success) {
			return $this->response($result->errors, self::HTTP_STATUS_400);
		}

		$data = $result->data;

		// Validate Request
		$isValid = $this->validateRequest($this->request->json);

		if ($isValid !== true) {
			return $this->response($isValid, self::HTTP_STATUS_400);
		}

		// Download main image and additional images
		try {
			$this->downloadImages($data);
		} catch (InvalidArgumentException $e) {
			return $this->response(
				array(
					'result' => false,
					'details' => 'Error downloading image files',
					'errors' => array(
						$e->getMessage()
					)
				),
				self::HTTP_STATUS_400
			);
		} catch (RuntimeException $e) {
			return $this->response(array(), self::HTTP_STATUS_500);
		}

		if ($product_id === null) {
			$result = $this->model_catalog_product->add($data);
			$product_info = $this->load->controller('product/info/index', $result);

			$this->load->controller('product/info/index', $result);
			$this->response->addHeader('HTTP/1.1 ' . self::HTTP_STATUS_201);
			return;
		}

		$result = $this->model_catalog_product->update($product_id, $data);
		$product_info = $this->load->controller('product/info/index', $result);

		return $this->load->controller('product/info/index', $result);
	}

	/**
	 * Download main image and additional images
	 *
	 * @param Object $data
	 *
	 * @return void
	 */
	protected function downloadImages(&$data) {
		$this->load->model('tool/image');

		$data->image = $this->model_tool_image->download($data->image);

		foreach ($data->additional_images as $key => $url) {
			$data->additional_images[$key] = $this->model_tool_image->download($url);
		}
	}

	/**
	 * Validate input JSON schema
	 *
	 * @param Object $data
	 *
	 * @return Object
	 */
	protected function validateJsonSchema($data) {
		$this->config->load('api/schemas/product_form');

		$jsonSchema = $this->config->get('api_schema_product_form');

		$schema = Schema::import($jsonSchema);

		$result = new \stdClass;
		$result->success = true;

		try {
			$result->data = $schema->in($data);
		} catch (LogicException $e) {
			if ($e->getFailedSubSchema($schema)->anyOf) {
				$items = $e->getFailedSubSchema($schema)->anyOf;
			} elseif ($e->getFailedSubSchema($schema)->oneOf) {
				$items = $e->getFailedSubSchema($schema)->oneOf;
			} elseif ($e->getFailedSubSchema($schema)->allOf) {
				$items = $e->getFailedSubSchema($schema)->allOf;
			}

			if ($items) {
				$types = array();

				foreach ($items as $value) {
					$types[] = $value->items->type;
				}

				$errors[] = array(
					'node' => $e->getDataPointer(),
					'details' => implode(' OR ', $types) . ' expected, just one type'
				);
			} else {
				$error = $e->inspect();

				$errors[] = array(
					'node' => $error->dataPointer,
					'details' => $error->error
				);
			}
		} catch (InvalidValue $e) {
			$error = $e->inspect();

			$errors[] = array(
				'node' => $error->dataPointer,
				'details' => $error->error
			);
		}

		if (!empty($errors) || empty($data)) {
			$result->success = false;
			$result->errors = array(
				'result' => false,
				'details' => 'Error filling in data sent',
				'errors' => $errors
			);
		}

		return $result;
	}

	/**
	 * Validate input data
	 *
	 * @param stdClass $data
	 *
	 * @return bool
	 */
	protected function validateRequest(\stdClass $data) {
		// Tax
		if (isset($data->tax_class_id)) {
			$this->load->model('localisation/tax_class');

			$tax_class = !!$this->model_localisation_tax_class->getTaxClass($data->tax_class_id);

			if ($tax_class === false) {
				$errors[] = array(
					'code' => 1,
					'message' => 'Tax group is invalid',
				);
			}
		}

		// Stock
		if (isset($data->stock_status_id)) {
			$this->load->model('localisation/stock_status');

			$stock_status = !!$this->model_localisation_stock_status->getStockStatus($data->stock_status_id);

			if ($stock_status === false) {
				$errors[] = array(
					'code' => 2,
					'message' => 'Non-existent stock situation',
				);
			}
		}

		// Dimensions
		if (isset($data->dimensions)) {
			$this->load->model('localisation/length_class');

			$dimensions = $data->dimensions;

			if (isset($dimensions->length_class_unit)) {
				$length_class = !!$this->model_localisation_length_class->getLengthClassIdByUnit($dimensions->length_class_unit);
			} else {
				$length_class = !!$this->model_localisation_length_class->getLengthClass($dimensions->length_class_id);
			}

			if ($stock_status === false) {
				$errors[] = array(
					'code' => 3,
					'message' => 'Non-existent dimension unit',
				);
			}

			unset($dimensions);
		}

		// Manufacturer
		if (isset($data->manufacturer_id)) {
			$this->load->model('catalog/manufacturer');

			$manufacturer = !!$this->model_catalog_manufacturer->getManufacturer($data->manufacturer_id);

			if ($manufacturer === false) {
				$errors[] = array(
					'code' => 4,
					'message' => 'Manufacturer not registered',
				);
			}
		}

		// Categories
		if (isset($data->categories)) {
			$this->load->model('catalog/category');

			$categories = $this->model_catalog_category->getHasCategoryById($data->categories);

			if ($categories !== true) {
				$errors[] = array(
					'code' => 5,
					'message' => 'Categories "' . implode(',', $categories) . '" do not exist'
				);
			}
		}

		// Filters
		if (isset($data->filters)) {
			$this->load->model('catalog/filter');

			$filters = $this->model_catalog_filter->getHasFilterById($data->filters);

			if ($filters !== true) {
				$errors[] = array(
					'code' => 6,
					'message' => 'Filters "' . implode(',', $filters) . '" do not exist'
				);
			}
		}

		// Stores
		if (isset($data->stores)) {
			$this->load->model('setting/store');

			$stores = $this->model_setting_store->getHasStoreById($data->stores);

			if ($stores !== true) {
				$errors[] = array(
					'code' => 7,
					'message' => 'Stores "' . implode(',', $stores) . '" do not exist'
				);
			}
		}

		// Download
		if (isset($data->downloads)) {
			$this->load->model('catalog/download');

			$downloads_id = array_filter($data->downloads, 'is_numeric');

			if ($downloads_id) {
				$downloads = $this->model_catalog_download->getHasDownloadById($downloads_id);

				if ($downloads !== true) {
					$errors[] = array(
						'code' => 8,
						'message' => 'Downloads "' . implode(',', $downloads) . '" do not exist'
					);
				}

				unset($downloads);
			}

			$downloads_url = array_filter($data->downloads, 'is_string');

			if ($downloads_url) {
				$this->validateUrl($errors, $downloads_url);
			}

			unset($downloads_id);
			unset($downloads_url);
		}

		// Product Related
		if (isset($data->product_related)) {
			$products_related = $this->model_catalog_product->getHasProductById($data->product_related);

			if ($products_related !== true) {
				$errors[] = array(
					'code' => 10,
					'message' => 'Products "' . implode(',', $products_related) . '" do not exist'
				);
			}

			unset($products_related);
		}

		// Attributes
		if (isset($data->attributes)) {
			$this->load->model('catalog/attribute');

			$attributes_id = array_map(function($attribute) {
				return $attribute->id;
			}, $data->attributes);

			$attributes = $this->model_catalog_attribute->getHasAttributeById($attributes_id);

			if ($attributes !== true) {
				$errors[] = array(
					'code' => 11,
					'message' => 'Attributes "' . implode(',', $attributes) . '" do not exist'
				);
			}

			foreach ($data->attributes as $attribute) {
				if (!isset($attribute->default)) {
					$errors[] = array(
						'code' => 12,
						'message' => 'The "default" field is mandatory for attributes'
					);
				}
			}

			unset($attributes);
		}

		// Options
		if (isset($data->options)) {
			$this->load->model('catalog/option');

			foreach ($data->options as $option) {
				if (in_array($option->type, array('radio', 'checkbox', 'select'))) {
					$option_id = $option->option_id;

					$option_exist = !!$this->model_catalog_option->getOption($option_id);

					if ($option_exist) {
						foreach ($option->values as $value) {
							$option_value_id = $value->option_value_id;

							$option_value_exist = !!$this->model_catalog_option->getOptionValueIsRelatedToOptionId($option_id, $option_value_id);

							if (!$option_value_exist) {
								$errors[] = array(
									'code' => 14,
									'message' => 'The option value "' . $option_value_id. '" does not exist in the option "' . $option_id . '"'
								);
							}
						}
					} else {
						$errors[] = array(
							'code' => 13,
							'message' => 'Option "' . $option_id . '" do not exist'
						);
					}
				}
			}

			unset($option);
			unset($option_id);
			unset($option_exist);
			unset($option_value_id);
			unset($option_value_exist);
		}

		// Recurring
		if (isset($data->recurring)) {
			$this->load->model('catalog/recurring');
			$this->load->model('customer/customer_group');

			$recurrings = $data->recurring;

			foreach ($recurrings as $recurring) {
				$recurring_exist = !!$this->model_catalog_recurring->getRecurring($recurring->recurring_id);

				if ($recurring_exist === false) {
					$errors[] = array(
						'code' => 15,
						'message' => 'Recurring "' . $recurring->recurring_id . '" do not exist'
					);
				}

				$this->validateCustomerGroupById($errors, (int)$recurring->customer_group_id);
			}

			unset($recurrings);
			unset($recurring);
			unset($recurring_exist);
			unset($customer_group_exist);
		}

		// Price special
		if (isset($data->special)) {
			$this->load->model('customer/customer_group');

			foreach ($data->special as $special) {
				$this->validateCustomerGroupById($errors, (int)$special->customer_group_id);
			}

			unset($special);
		}

		// Price discount
		if (isset($data->discount)) {
			$this->load->model('customer/customer_group');

			foreach ($data->discount as $discount) {
				$this->validateCustomerGroupById($errors, (int)$discount->customer_group_id);
			}

			unset($discount);
		}

		// Image
		if (isset($data->image)) {
			$this->validateUrl($errors, (array)$data->image);
		}

		// Additional Images
		if (isset($data->additional_images)) {
			$this->validateUrl($errors, $data->additional_images);
		}

		// Points Reward
		if (isset($data->points_reward)) {
			$this->load->model('customer/customer_group');

			foreach ($data->points_reward as $points_reward) {
				$this->validateCustomerGroupById($errors, (int)$points_reward->customer_group_id);
			}

			unset($points_reward);
		}

		if (!empty($errors)) {
			return array(
				'result' => false,
				'details' => 'Error filling in data sent',
				'errors' => $errors
			);
		}

		return true;
	}

	/**
	 * Validate URL. They must follow the pattern defined in and start with "http" or "https"
	 *
	 * @param array $errors
	 * @param array $urls
	 *
	 * @return void
	 */
	protected function validateUrl(&$errors, array $urls = array()) {
		foreach ($urls as $value) {
			if (
				!filter_var($value, FILTER_VALIDATE_URL)
				|| !preg_match('/^https?:\/\//', $value)
			) {
				$errors[] = array(
					'code' => 9,
					'message' => 'URL "' . $value . '" is invalid'
				);
			}
		}
	}

	/**
	 * Validate URL. They must follow the pattern defined in and start with "http" or "https"
	 *
	 * @param array $errors
	 * @param array $urls
	 *
	 * @return void
	 */
	protected function validateCustomerGroupById(&$errors, int $customer_group_id = -1) {
		$customer_group_exist = false;

		if ($customer_group_id > 0) {
			$customer_group_exist = !!$this->model_customer_customer_group->getCustomerGroup($customer_group_id);
		}

		if ($customer_group_exist === false) {
			$errors[] = array(
				'code' => 16,
				'message' => 'User Group "' . $customer_group_id . '" do not exist'
			);
		}
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
