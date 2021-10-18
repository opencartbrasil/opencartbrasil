<?php
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\Exception\LogicException;
use Swaggest\JsonSchema\InvalidValue;

class ControllerOrderFormHistory extends Controller {
	private const HTTP_STATUS_400 = 400;

	public function index() {
		$this->load->model('sale/order');

		// Validate Schema
		$result = $this->validateJsonSchema($this->request->json);

		if (!$result->success) {
			return $this->response($result->errors, self::HTTP_STATUS_400);
		}

		$data = $result->data;

		$response = array();

		foreach ($data as $order) {
			$this->model_sale_order->addOrderHistory(
				$order->order_id,
				$order->order_status_id,
				$order->comment
			);

			$response[] = array(
				'order_id' => $order->order_id,
				'updated' => true
			);
		}

		return $this->response($response);
	}

	/**
	 * Validate input JSON schema
	 *
	 * @param Object $data
	 *
	 * @return Object
	 */
	protected function validateJsonSchema($data) {
		$this->config->load('api/schemas/order_form');

		$jsonSchema = $this->config->get('api_schema_order_form_history');

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
