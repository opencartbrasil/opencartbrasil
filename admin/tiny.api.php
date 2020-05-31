<?php
error_reporting(0);
ini_set("display_errors", "0");

/* 
 * Tiny OpenCart: Integration Module
 * Copyright (C) 2012  Tiny Software
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once ("config.php");
require_once (DIR_SYSTEM . "startup.php");

switch ($_REQUEST["method"]) {
	case "listOrders":
		if (empty($_REQUEST["criteriaDate"])) {
			$_REQUEST["criteriaDate"] = "added";
		}
		print_r(getListOrders($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["status"], $_REQUEST["initialDate"], $_REQUEST["finalDate"], $_REQUEST["criteriaDate"]));
		break;
	case "getOrder":
		print_r(getOrder($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["orderId"], $_REQUEST["aditionalFields"], $_REQUEST["productCodeField"], $_REQUEST["productAditionalFields"]));
		break;
	case "listProducts":
		print_r(getProducts($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["productCodeField"], $_REQUEST["aditionalFields"], $_REQUEST["page"], $_REQUEST["limit"]));
		break;
	case "listProductsAndOptions":
		print_r(getProductsAndOptions($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["productCodeField"], $_REQUEST["aditionalFields"], $_REQUEST["page"], $_REQUEST["limit"]));
		break;
	case "getProduct":
		print_r(getProduct($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["productId"], $_REQUEST["productCodeField"], $_REQUEST["aditionalFields"]));
		break;
	case "listStatus":
		print_r(getListStatus($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"]));
		break;
	case "insertUpdateProduct":
		print_r(insertUpdateProduct($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["productData"], $_REQUEST["productCodeField"]));
		break;
	case "setProductStockQuantity":
		print_r(setProductStockQuantity($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["productData"], $_REQUEST["productCodeField"]));
		break;
	case "testConfiguration":
		print_r(getTestConfiguration($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"]));
		break;
	case "updateShippingCode":
		print_r(updateShippingCode($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["shippingData"], $_REQUEST["orderNumberField"]));
		break;
	case "updateOrderStatus":
	    print_r(updateOrderStatus($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["orderData"]));
		break;
	case "setProductPrice":
		print_r(setProductPrice($_REQUEST["user"], $_REQUEST["password"], $_REQUEST["version"], $_REQUEST["productData"], $_REQUEST["productCodeField"]));
		break;
	default:
		print_r(json_encode(array("result" => "Error", "errorDetails" => "Método inválido ou não informado.")));
}

// === Classes ============================================================================
class Order {
	public $id;
	public $client;
	public $payment_method;
	public $payment_code;
	public $transaction_id;
	public $total;
	public $status;
	public $comment;
	public $freight;
	public $items;
	public $date;
	public $discount;
	public $shipping_address;
	public $payment_address;
	public $shipping_method;
	public $shipping_code;
	
	function __construct($id, $date, $payment_method, $total, $status, $comment, $freight, $discount, $payment_code = "", $transaction_id = "", $shipping_method = "", $shipping_code = "") {
		$this->id = $id;
		$this->date = $date;
		$this->payment_method = $payment_method;
		$this->total = $total;
		$this->status = $status;
		$this->comment = $comment;
		$this->freight = $freight;
		$this->discount = $discount;
		$this->payment_code = $payment_code;
		$this->transaction_id = $transaction_id;
		$this->shipping_method = $shipping_method;
		$this->shipping_code = $shipping_code;
		$this->items = array();
	}
	
	public function setClient(Client $client) {
		$this->client = $client;
	}
	
	public function setShippingAddress(Address $address) {
		$this->shipping_address = $address;
	}
	
	public function setPaymentAddress(Address $address) {
		$this->payment_address = $address;
	}
	
	public function addItem(Item $item) {
		$this->items[] = $item;
	}
}

class Item {
	public $product;
	public $quantity;
	public $price;
	public $total;
	
	function __construct(Product $product, $quantity, $price, $total) {
		$this->product = $product;
		$this->quantity = $quantity;
		$this->price = $price;
		$this->total = $total;
	}
}

class Client {
	public $name;
	public $mail;
	public $phone;
	public $phone2;
	public $address;
	public $aditionalFields;
	
	function __construct($firstName, $lastName, $mail, $phone, $phone2) {
		$this->name = $firstName . " " . $lastName;
		$this->mail = $mail;
		$this->phone = $phone;
		$this->phone2 = $phone2;
		$aditionalFields = array();
	}
	
	public function setAddress(Address $address) {
		$this->address = $address;
	}
	
	public function addAditionalField($key, $value) {
		$this->aditionalFields[$key] = $value;
	}
}

class Address {
	public $address;
	public $neighborhood;
	public $city;
	public $postcode;
	public $country;
	public $state;
	
	function __construct($address, $neighborhood, $city, $postcode, $country, $state) {
		$this->address = $address;
		$this->neighborhood = $neighborhood;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->country = $country;
		$this->state = $state;
	}
}

class Product {
	public $name;
	public $name_and_options;
	public $model;
	public $id;
	public $price;
	public $stock_quantity;
	public $weight;
	public $aditionalFields;
	public $categories;
	public $special_price;
	public $height;
	public $width;
	public $length;
	public $images;
	
	function __construct($name, $name_and_options, $model, $id = null, $price = null, $stock_quantity = null, $weight = null, $categories = array(), $special_price = 0, $height = null, $width = null, $length = null) {
		$this->name = $name;
		$this->name_and_options = $name_and_options;
		$this->model = $model;
		$this->id = $id;
		$this->price = $price;
		$this->stock_quantity = $stock_quantity;
		$this->weight = $weight;
		$this->categories = $categories;
		$this->special_price = $special_price;
		$this->height = $height;
		$this->width = $width;
		$this->length = $length;
		$this->images = array();
		$aditionalFields = array();
	}
	
	public function addAditionalField($key, $value) {
		$this->aditionalFields[$key] = $value;
	}
}

// === SQLs ============================================================================
function sql_getUser($user, $password, $version) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	
	switch ($version) {
		case "1.5.1":
		case "1.5.2":
		case "1.5.3":
			$query = $db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . $db->escape($user) . "' AND password = '" . $db->escape(md5($password)) . "' AND status = '1'");
			break;
		default:
			$query = $db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE username = '" . addslashes($user) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . addslashes($password) . "'))))) OR password = '" . addslashes(md5($password)) . "') AND status = '1'");
			break;
	}
	return $query->rows;
}

function sql_getLanguageId() {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT MAX(language_id) AS 'language_id' FROM `" . DB_PREFIX . "language`");
	foreach ($query->rows as $language) {
		return $language["language_id"];
	}
}

function sql_getOrders($status, $initialDate, $finalDate, $criteriaDate = "added") {
	$languageId = sql_getLanguageId();
	$fieldDate = "o.date_added";
	if ($criteriaDate == "modified") {
		$fieldDate = "o.date_modified";
	}
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$sql = "SELECT o.*, s.name AS 'status' FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_status` s ON o.order_status_id = s.order_status_id WHERE s.language_id = '" . $languageId . "' AND o.order_status_id <> 0 AND " . $fieldDate . " >= '" . $initialDate . "' AND " . $fieldDate . " <= '" . $finalDate . " 23:59:59' ";
	if (trim($status) != "") {
		$sql .= "AND s.name = '" . $status . "' ";
	}
	$sql .= "ORDER BY o.order_id DESC";
	$query = $db->query($sql);
	return $query->rows;
}

function sql_getOrderStatus() {
	$languageId = sql_getLanguageId();
	
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$sql = "SELECT s.name AS 'status' FROM `" . DB_PREFIX . "order_status` s WHERE s.language_id = '" . $languageId . "' ORDER BY s.name";
	$query = $db->query($sql);
	return $query->rows;
}

function sql_getCustomer($customerId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT c.*, a.address_1, a.address_2, a.city, a.postcode, z.name AS zone, ct.name AS country FROM `" . DB_PREFIX . "customer` c LEFT JOIN `" . DB_PREFIX . "address` a ON c.address_id = a.address_id LEFT JOIN `" . DB_PREFIX . "zone` z ON a.zone_id = z.zone_id LEFT JOIN `" . DB_PREFIX . "country` ct ON a.country_id = ct.country_id WHERE c.customer_id = '" . $customerId . "'");
	return $query->rows;
}

function sql_getOrder($orderId) {
	$languageId = sql_getLanguageId();
	
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT o.*, s.name AS 'status' FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_status` s ON o.order_status_id = s.order_status_id WHERE o.order_id = '" . $orderId . "' AND s.language_id = '" . $languageId . "'");
	return $query->rows;
}

function sql_getTransactionIdPagarme($orderId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	$query = $db->query("SELECT transaction_id FROM `" . DB_PREFIX . "pagar_me_transaction` WHERE order_id = '" . $db->escape($orderId) . "' LIMIT 1");

	return $query->num_rows ? $query->row["transaction_id"] : 0;
}

function sql_getTransactionIdPagarmeCheckout($orderId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	$query = $db->query("SELECT transaction_id FROM `" . DB_PREFIX . "pagar_me_checkout_transaction` WHERE order_id = '" . $db->escape($orderId) . "' LIMIT 1");

	return $query->num_rows ? $query->row["transaction_id"] : 0;
}

function sql_getOrderFreight($orderId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . $orderId . "' AND code = 'shipping'");
	$freightValue = 0;
	foreach ($query->rows as $resultFreight) {
		$freightValue += $resultFreight["value"];
	}
	return $freightValue;
}

function sql_getOrderItems($orderId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT p.model, p.sku, o.quantity AS item_quantity, o.price AS item_price, o.*, p.* FROM `" . DB_PREFIX . "order_product` o JOIN `" . DB_PREFIX . "product` p ON o.product_id = p.product_id WHERE o.order_id = '" . $orderId . "'");
	return $query->rows;
}

function sql_insertProductSpecialPrice($productId, $specialPrice) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$db->query("INSERT INTO `" . DB_PREFIX . "product_special` SET `product_id` = '" . $productId . "', `customer_group_id` = '1', `price` = '" . $specialPrice . "'");
}

function sql_deleteProductSpecialPrices($productId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$db->query("DELETE FROM `" . DB_PREFIX . "product_special` WHERE product_id = '" . $productId . "'");
}

function sql_getProductSpecialPrices($productId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT pc.price, pc.date_start, pc.date_end FROM `" . DB_PREFIX . "product_special` pc WHERE pc.product_id = '" . $productId . "' ORDER BY pc.priority ASC, pc.price ASC, pc.product_special_id ASC ");
	return $query->rows;
}

function sql_getProductCategories($productId) {
	$languageId = sql_getLanguageId();

	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT c.category_id, c.parent_id, cd.name FROM `" . DB_PREFIX . "product_to_category` pc JOIN `" . DB_PREFIX . "category` c ON c.category_id = pc.category_id JOIN `" . DB_PREFIX . "category_description` cd ON cd.category_id = pc.category_id WHERE pc.product_id = '" . $productId . "' AND cd.language_id = '" . $languageId . "' ");
	return $query->rows;
}

function sql_deleteProductCategories($productId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE product_id = '" . $productId . "'");
}

function sql_addProductCategory($productId, $categoryId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$db->query("INSERT INTO `" . DB_PREFIX . "product_to_category` SET `product_id` = '" . $productId . "', `category_id` = '" . $categoryId . "'");
}

function sql_addCategory($data) {
	// admin/model/catalog/category.php, addCategory
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	$db->query("INSERT INTO " . DB_PREFIX . "category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (((int)$data['parent_id'] > 0) ? 0 : 1) . "', `column` = '0', sort_order = '0', status = '1', date_modified = NOW(), date_added = NOW()");

	$categoryId = $db->getLastId();

	foreach ($data['category_description'] as $languageId => $value) {
		$db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$categoryId . "', language_id = '" . (int)$languageId . "', name = '" . $db->escape($value['name']) . "', description = '" . $db->escape($value['name']) . "'");
	}

	// MySQL Hierarchical Data Closure Table Pattern
	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

	$level = 0;
	foreach ($query->rows as $result) {
		$db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$categoryId . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");
		$level++;
	}

	$db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$categoryId . "', `path_id` = '" . (int)$categoryId . "', `level` = '" . (int)$level . "'");

	$db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$categoryId . "', store_id = '0'");

	return $categoryId;
}

function sql_getCategory($categoryId) {
	$languageId = sql_getLanguageId();

	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT c.category_id, c.parent_id, cd.name FROM `" . DB_PREFIX . "category` c JOIN `" . DB_PREFIX . "category_description` cd ON cd.category_id = c.category_id WHERE c.category_id = '" . (int)$categoryId . "' AND cd.language_id = '" . (int)$languageId . "' ");
	return $query->rows;
}

function sql_getCategoryByName($categoryName, $parentId) {
	$languageId = sql_getLanguageId();

	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT c.category_id, c.parent_id, cd.name FROM `" . DB_PREFIX . "category` c JOIN `" . DB_PREFIX . "category_description` cd ON cd.category_id = c.category_id WHERE cd.name = '" . $db->escape($categoryName) . "' AND cd.language_id = '" . (int)$languageId . "' AND c.parent_id = '" . (int)$parentId . "'");
	return $query->rows;
}

function sql_getProductImages($productId) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $productId . "' ORDER BY sort_order");
	return $query->rows;
}

function sql_getProducts() {
	$languageId = sql_getLanguageId();
	
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT p.product_id, pd.name, p.model, p.sku, p.quantity, p.price, p.weight, p.weight, p.length, p.height, p.* FROM `" . DB_PREFIX . "product` p JOIN `" . DB_PREFIX . "product_description` pd ON p.product_id = pd.product_id WHERE pd.language_id = '" . $languageId . "' ORDER BY pd.name");
	return $query->rows;
}

function sql_getProduct($productCodeField, $productId) {
	$productCodeField = $productCodeField == "M" ? "model" : "sku";

	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT p.product_id, pd.name, p.model, p.sku, p.quantity, p.price, p.weight, p.length, p.height, p.width, p.* FROM `" . DB_PREFIX . "product` p JOIN `" . DB_PREFIX . "product_description` pd ON p.product_id = pd.product_id WHERE p." . $productCodeField . " = '" . $productId . "' ORDER BY pd.name");

	return $query->rows;
}

function sql_getOrderProductOptions($order_product_id) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE order_product_id = '" . $order_product_id . "'");
	return $query->rows;
}

function sql_getProductOptions($productId) {
	$languageId = sql_getLanguageId();
	
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT o.option_id, o.quantity, o.price, o.weight, d.name, t.sort_order FROM `" . DB_PREFIX . "product_option_value` o JOIN `" . DB_PREFIX . "option_value_description` d ON o.option_value_id = d.option_value_id JOIN `" . DB_PREFIX . "option` t ON o.option_id = t.option_id WHERE o.product_id = '" . $productId . "' AND d.language_id = '" . $languageId . "' ORDER BY t.sort_order");
	return $query->rows;
}

function sql_getOrderTotal($orderId) {
    $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $query = $db->query("SELECT `value`, '0' AS shipping FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . $orderId . "' AND code <> 'coupon' UNION SELECT ch.amount, c.shipping FROM `" . DB_PREFIX . "coupon_history` ch JOIN `" . DB_PREFIX . "coupon` c ON ch.coupon_id = c.coupon_id WHERE ch.order_id = '" . $orderId . "'");
    return $query->rows;
}

function sql_updateShippingCode($shippingData) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $db->query("UPDATE `" . DB_PREFIX . "order` SET shipping_method = '" . $shippingData->shipping_method . "', shipping_code = '" . $shippingData->shipping_code . "', order_status_id = '3' WHERE order_id = '" . $shippingData->orderId . "';");
    $db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . $shippingData->orderId . "', order_status_id = '3', notify = '0', comment = 'Código de Rastreio: " . $shippingData->shipping_code ."', date_added = NOW()");
}

function sql_updateOrderStatus($orderData) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . $orderData->order_status_id . "' WHERE order_id = '" . $orderData->order_id . "';");
    $db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . $orderData->order_id . "', order_status_id = '" . $orderData->order_status_id . "', notify = '0', comment = '', date_added = NOW()");
}

function sql_insertUpdateProduct($data, $version) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $data["id"] . "'");
	if ($query->num_rows) {
		$db->query("DELETE FROM `" . DB_PREFIX . "product_to_store` WHERE product_id = '" . $data["id"] . "' AND store_id = '0'");

		if (isset($data["product_image"])) {
			if (count($data["product_image"]) > 0) {
				$db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $data["id"] . "'");
			}
		}

		$dimensions = "";
		if (isset($data["length"], $data["width"], $data["height"])) {
			$dimensions = ", length = '" . (float)$data["length"] . "', width = '" . (float)$data["width"] . "', height = '" . (float)$data["height"] . "' ";
		}

		switch ($version) {
			case "1.5.1":
			case "1.5.2":
			case "1.5.3":
				if(isset($data["quantity"])) {
					$db->query("UPDATE `" . DB_PREFIX . "product` SET model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', quantity = '" . (int)$data["quantity"] . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), price = '" . (float)$data["price"] . "', weight = '" . (float)$data["weight"] . "', status = '" . (int)$data["status"] . "', date_added = NOW() " . $dimensions . " WHERE product_id = '" . $db->escape($data["id"]) . "';");
				} else {
					$db->query("UPDATE `" . DB_PREFIX . "product` SET model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), price = '" . (float)$data["price"] . "', weight = '" . (float)$data["weight"] . "', status = '" . (int)$data["status"] . "', date_added = NOW() " . $dimensions . " WHERE product_id = '" . $db->escape($data["id"]) . "';");
				}
				break;
			default:
				if(isset($data["quantity"])) {
					$db->query("UPDATE `" . DB_PREFIX . "product` SET model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', ean = '" . $db->escape($data["ean"]) . "', quantity = '" . (int)$data["quantity"] . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), price = '" . (float)$data["price"] . "', weight = '" . (float)$data["weight"] . "', status = '" . (int)$data["status"] . "', date_added = NOW() " . $dimensions . " WHERE product_id = '" . $db->escape($data["id"]) . "';");
				} else {
					$db->query("UPDATE `" . DB_PREFIX . "product` SET model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', ean = '" . $db->escape($data["ean"]) . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), price = '" . (float)$data["price"] . "', weight = '" . (float)$data["weight"] . "', status = '" . (int)$data["status"] . "', date_added = NOW() " . $dimensions . " WHERE product_id = '" . $db->escape($data["id"]) . "';");
				}
				break;
		}

		$productId = $data["id"];
	} else {
		switch ($version) {
			case "1.5.1":
			case "1.5.2":
			case "1.5.3":
				if(isset($data["quantity"])) {
					$db->query("INSERT INTO `" . DB_PREFIX . "product` SET product_id = '" . $db->escape($data["id"]) . "', model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', upc = '" . $db->escape($data["upc"]) . "', location = '" . $db->escape($data["location"]) . "', quantity = '" . (int)$data["quantity"] . "', minimum = '" . (int)$data["minimum"] . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), manufacturer_id = '" . (int)$data["manufacturer_id"] . "', price = '" . (float)$data["price"] . "', points = '" . (int)$data["points"] . "', weight = '" . (float)$data["weight"] . "', weight_class_id = '" . (int)$data["weight_class_id"] . "', length = '" . (float)$data["length"] . "', width = '" . (float)$data["width"] . "', height = '" . (float)$data["height"] . "', length_class_id = '" . (int)$data["length_class_id"] . "', status = '" . (int)$data["status"] . "', tax_class_id = '" . $db->escape($data["tax_class_id"]) . "', sort_order = '" . (int)$data["sort_order"] . "', date_added = NOW()");
				} else {
					$db->query("INSERT INTO `" . DB_PREFIX . "product` SET product_id = '" . $db->escape($data["id"]) . "', model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', upc = '" . $db->escape($data["upc"]) . "', location = '" . $db->escape($data["location"]) . "', minimum = '" . (int)$data["minimum"] . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), manufacturer_id = '" . (int)$data["manufacturer_id"] . "', price = '" . (float)$data["price"] . "', points = '" . (int)$data["points"] . "', weight = '" . (float)$data["weight"] . "', weight_class_id = '" . (int)$data["weight_class_id"] . "', length = '" . (float)$data["length"] . "', width = '" . (float)$data["width"] . "', height = '" . (float)$data["height"] . "', length_class_id = '" . (int)$data["length_class_id"] . "', status = '" . (int)$data["status"] . "', tax_class_id = '" . $db->escape($data["tax_class_id"]) . "', sort_order = '" . (int)$data["sort_order"] . "', date_added = NOW()");
				}
				break;
			default:
				if(isset($data["quantity"])) {
					$db->query("INSERT INTO `" . DB_PREFIX . "product` SET product_id = '" . $db->escape($data["id"]) . "', model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', upc = '" . $db->escape($data["upc"]) . "', ean = '" . $db->escape($data["ean"]) . "', jan = '" . $db->escape($data["jan"]) . "', isbn = '" . $db->escape($data["isbn"]) . "', mpn = '" . $db->escape($data["mpn"]) . "', location = '" . $db->escape($data["location"]) . "', quantity = '" . (int)$data["quantity"] . "', minimum = '" . (int)$data["minimum"] . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), manufacturer_id = '" . (int)$data["manufacturer_id"] . "', price = '" . (float)$data["price"] . "', points = '" . (int)$data["points"] . "', weight = '" . (float)$data["weight"] . "', weight_class_id = '" . (int)$data["weight_class_id"] . "', length = '" . (float)$data["length"] . "', width = '" . (float)$data["width"] . "', height = '" . (float)$data["height"] . "', length_class_id = '" . (int)$data["length_class_id"] . "', status = '" . (int)$data["status"] . "', tax_class_id = '" . $db->escape($data["tax_class_id"]) . "', sort_order = '" . (int)$data["sort_order"] . "', date_added = NOW()");
				} else {
					$db->query("INSERT INTO `" . DB_PREFIX . "product` SET product_id = '" . $db->escape($data["id"]) . "', model = '" . $db->escape($data["model"]) . "', sku = '" . $db->escape($data["sku"]) . "', upc = '" . $db->escape($data["upc"]) . "', ean = '" . $db->escape($data["ean"]) . "', jan = '" . $db->escape($data["jan"]) . "', isbn = '" . $db->escape($data["isbn"]) . "', mpn = '" . $db->escape($data["mpn"]) . "', location = '" . $db->escape($data["location"]) . "', minimum = '" . (int)$data["minimum"] . "', subtract = '" . (int)$data["subtract"] . "', stock_status_id = '" . (int)$data["stock_status_id"] . "', date_available = NOW(), manufacturer_id = '" . (int)$data["manufacturer_id"] . "', price = '" . (float)$data["price"] . "', points = '" . (int)$data["points"] . "', weight = '" . (float)$data["weight"] . "', weight_class_id = '" . (int)$data["weight_class_id"] . "', length = '" . (float)$data["length"] . "', width = '" . (float)$data["width"] . "', height = '" . (float)$data["height"] . "', length_class_id = '" . (int)$data["length_class_id"] . "', status = '" . (int)$data["status"] . "', tax_class_id = '" . $db->escape($data["tax_class_id"]) . "', sort_order = '" . (int)$data["sort_order"] . "', date_added = NOW()");
				}
				break;
		}
		$productId = $db->getLastId();
	}
	
	if (isset($data["image"])) {
		$db->query("UPDATE `" . DB_PREFIX . "product` SET image = '" . $db->escape(html_entity_decode($data["image"], ENT_QUOTES, "UTF-8")) . "' WHERE product_id = '" . (int)$productId . "'");
	}
		
	foreach ($data["product_description"] as $languageId => $value) {
		$query = $db->query("SELECT name FROM `" . DB_PREFIX . "product_description` WHERE product_id = '" . (int)$productId . "' AND language_id = '" . (int)$languageId . "'");
		if ($query->num_rows) {
			$currentOCDescription = $query->rows[0]["name"];
			if ($currentOCDescription != $value["name"]) {
				$db->query("UPDATE `" . DB_PREFIX . "product_description` SET name = '" . $db->escape($value["name"]) . "', meta_description = '" . $db->escape($value["description"]) . "', description = '" . $db->escape($value["description"]) . "' WHERE product_id = '" . (int)$productId . "' AND language_id = '" . (int)$languageId . "'");
			}
		} else {
			$db->query("INSERT INTO `" . DB_PREFIX . "product_description` SET product_id = '" . (int)$productId . "', language_id = '" . (int)$languageId . "', name = '" . $db->escape($value["name"]) . "', meta_keyword = '" . $db->escape($value["meta_keyword"]) . "', meta_description = '" . $db->escape($value["description"]) . "', description = '" . $db->escape($value["description"]) . "'");
		}
	}
		
	if (isset($data["product_image"])) {
		foreach ($data["product_image"] as $product_image) {
			$db->query("INSERT INTO `" . DB_PREFIX . "product_image` SET product_id = '" . (int)$productId . "', image = '" . $db->escape(html_entity_decode($product_image["image"], ENT_QUOTES, "UTF-8")) . "', sort_order = '" . (int)$product_image["sort_order"] . "'");
		}
	}
	
	$db->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET product_id = '" . (int)$productId . "', store_id = '0'");
}

function sql_setProductStockQuantity($data) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $data["id"] . "'");
	if ($query->num_rows) {
		$db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = '" . (int)$data["quantity"] . "' WHERE product_id = '" . $db->escape($data["id"]) . "';");
	}
}

function sql_setProductPrice($data) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$query = $db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $data["id"] . "'");
	if ($query->num_rows) {
		$db->query("UPDATE `" . DB_PREFIX . "product` SET price = '" . (float)$data["price"] . "' WHERE product_id = '" . $db->escape($data["id"]) . "';");
	}
}

function sql_getProductIdByCode($id, $code, $codeField) {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$productId = $id;
	$query = $db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $id . "'");
	if (! ($query->num_rows)) {
		if ($codeField == "S") {
			$query = $db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE sku = '" . $code . "'");
		} else {
			$query = $db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE model = '" . $code . "'");
		}
		if ($query->num_rows) {
			foreach ($query->rows as $resultProduct) {
				$productId = $resultProduct["product_id"];
			}
		}
	}
	return $productId;
}

function sql_getCustomFields() {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$languageId = sql_getLanguageId();
	$query = $db->query("SELECT cf.*, cfd.* FROM `" . DB_PREFIX . "custom_field` cf JOIN `" . DB_PREFIX . "custom_field_description` cfd ON cf.custom_field_id = cfd.custom_field_id WHERE cfd.language_id = '" . $languageId . "'");

	$customFields = null;
	if ($query->num_rows) {
		foreach ($query->rows as $resultCustom) {
			$customFields[] = array("id" => $resultCustom["custom_field_id"], "name" => $resultCustom["name"]);
		}
	}

	return $customFields;
}

// === Methods ============================================================================
function getTestConfiguration($user, $password, $version) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		return json_encode(array("result" => "Ok"));
	}
}

function getListStatus($user, $password, $version) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		$listStatus = array();
		$db_status = sql_getOrderStatus();
		foreach ($db_status as $result) {
			$listStatus[] = $result["status"];
		}
		return json_encode(array("result" => "Ok", "data" => $listStatus));
	}
}

function getListOrders($user, $password, $version, $status, $initialDate, $finalDate, $criteriaDate = "added") {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		$listOrders = array();
		$db_orders = sql_getOrders($status, $initialDate, $finalDate, $criteriaDate);
		foreach ($db_orders as $result) {
			$objOrder = new Order($result["order_id"], $result["date_added"], $result["payment_method"], ($result["total"] * $result["currency_value"]), $result["status"], "", 0, 0);
			$objOrder->client = new Client($result["firstname"], $result["lastname"], $result["email"], $result["telephone"], $result["fax"]);
			$listOrders[] = $objOrder;
		}
		return json_encode(array("result" => "Ok", "data" => $listOrders));
	}
}

function getOrder($user, $password, $version, $orderId, $aditionalFields, $productCodeField, $productAditionalFields) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		$db_order = sql_getOrder($orderId);
		foreach ($db_order as $result) {
			$freightValue = (sql_getOrderFreight($orderId) * $result["currency_value"]);
			$discount = 0;
			
			$db_total = sql_getOrderTotal($orderId);
            foreach ($db_total as $resultTotal) {
                if ($resultTotal["value"] < 0) {
                	if ($resultTotal["shipping"] == 1) {
                		$discount += (abs($resultTotal["value"]) - $freightValue);
						$freightValue = 0;
                	} else {
                		$discount += abs($resultTotal["value"]);
                	}
                }
            }

            $transaction_id = 0;
            try {
	            if ($result["payment_code"] === "pagar_me_boleto" || $result["payment_code"] === "pagar_me_cartao") {
	            	$transaction_id = sql_getTransactionIdPagarme($orderId);
	            } else if ($result["payment_code"] === "pagar_me_checkout") {
	            	$transaction_id = sql_getTransactionIdPagarmeCheckout($orderId);
	            }
	        } catch(Exception $e) {}

			$order = new Order($result["order_id"], $result["date_added"], $result["payment_method"], ($result["total"] * $result["currency_value"]), $result["status"], $result["comment"], $freightValue, $discount, $result["payment_code"], $transaction_id, $result["shipping_method"], $result["shipping_code"]);
			$client = new Client($result["firstname"], $result["lastname"], $result["email"], $result["telephone"], $result["fax"]);
			
			//$aditionalFields = urldecode($aditionalFields);
			//$productAditionalFields = urldecode($productAditionalFields);
			
			if ($result["customer_id"] > 0) {
				$db_customer = sql_getCustomer($result["customer_id"]);
				$resultCustomer = $db_customer[0];
				$address = new Address($resultCustomer["address_1"], $resultCustomer["address_2"], $resultCustomer["city"], $resultCustomer["postcode"], $resultCustomer["country"], $resultCustomer["zone"]);
				
				$aditionalFields = explode(",", $aditionalFields);
				if (isset($resultCustomer)) {
					foreach ($resultCustomer as $keyField => $valueField) {
						if (in_array($keyField, $aditionalFields)) {
							$client->addAditionalField($keyField, $valueField);
						}
					}
				}
				
				if (isset($result)) {
					foreach ($result as $keyField => $valueField) {
						if (in_array($keyField, $aditionalFields)) {
							$client->addAditionalField($keyField, $valueField);
						}
					}
				}
				
				if ($version == "2.0.0") {
					$aFields = sql_getCustomFields();
					$customFields = json_decode($result["custom_field"], true);
					if ($customFields) {
						foreach ($aFields as $aField) {
							if (in_array($aField["name"], $aditionalFields)) {
								foreach ($customFields as $key => $value) {
									if ($key == $aField["id"]) {
										$client->addAditionalField($aField["name"], $value);
									}
								}
							}
						}
					} else {
						$customFields = unserialize($result["custom_field"]);
						foreach ($aFields as $aField) {
							if (in_array($aField["name"], $aditionalFields)) {
								if (isset($customFields[$aField["id"]])) {
									$client->addAditionalField($aField["name"], $customFields[$aField["id"]]);
								}
							}
						}
					}
						
					$paymentCustomFields = json_decode($result["payment_custom_field"], true);
					if ($paymentCustomFields) {
						foreach ($aFields as $aField) {
							if (in_array($aField["name"], $aditionalFields)) {
								foreach ($paymentCustomFields as $key => $value) {
									if ($key == $aField["id"]) {
										$client->addAditionalField("payment_" . $aField["name"], $value);
									}
								}
							}
						}
					} else {
						$paymentCustomFields = unserialize($result["payment_custom_field"]);
						foreach ($aFields as $aField) {
							if (in_array($aField["name"], $aditionalFields)) {
								if (isset($paymentCustomFields[$aField["id"]])) {
									$client->addAditionalField("payment_" . $aField["name"], $paymentCustomFields[$aField["id"]]);
								}
							}
						}
					}
					
					$shippingCustomFields = json_decode($result["shipping_custom_field"], true);
					if ($shippingCustomFields) {
						foreach ($aFields as $aField) {
							if (in_array($aField["name"], $aditionalFields)) {
								foreach ($shippingCustomFields as $key => $value) {
									if ($key == $aField["id"]) {
										$client->addAditionalField("shipping_" . $aField["name"], $value);
									}
								}
							}
						}
					} else {
						$shippingCustomFields = unserialize($result["shipping_custom_field"]);
						foreach ($aFields as $aField) {
							if (in_array($aField["name"], $aditionalFields)) {
								if (isset($shippingCustomFields[$aField["id"]])) {
									$client->addAditionalField("shipping_" . $aField["name"], $shippingCustomFields[$aField["id"]]);
								}
							}
						}
					}
				}
			} else {
				$address = new Address($result["shipping_address_1"], $result["shipping_address_2"], $result["shipping_city"], $result["shipping_postcode"], $result["shipping_country"], $result["shipping_zone"]);
			}
			
			$shipping_address = new Address($result["shipping_address_1"], $result["shipping_address_2"], $result["shipping_city"], $result["shipping_postcode"], $result["shipping_country"], $result["shipping_zone"]);
			$payment_address = new Address($result["payment_address_1"], $result["payment_address_2"], $result["payment_city"], $result["payment_postcode"], $result["payment_country"], $result["payment_zone"]);
			
			$client->setAddress($address);
			$order->setClient($client);
			$order->setShippingAddress($shipping_address);
			$order->setPaymentAddress($payment_address);
			
			$db_items = sql_getOrderItems($orderId);
			foreach ($db_items as $resultItem) {
				if ($productCodeField == "S") {
					$productItem = new Product($resultItem["name"], $resultItem["name"] . getProductNameAndOptions($resultItem["order_product_id"]), $resultItem["sku"], $resultItem["product_id"]);
					$arProductAditionalFields = explode(",", $productAditionalFields);
					if (isset($resultItem)) {
						foreach ($resultItem as $keyField => $valueField) {
							if (in_array($keyField, $arProductAditionalFields)) {
								$productItem->addAditionalField($keyField, $valueField);
							}
						}
					}
					$order->addItem(new Item($productItem, $resultItem["item_quantity"], ($resultItem["item_price"] * $result["currency_value"]), ($resultItem["total"] * $result["currency_value"])));
				} else {
					$productItem = new Product($resultItem["name"], $resultItem["name"] . getProductNameAndOptions($resultItem["order_product_id"]), $resultItem["model"], $resultItem["product_id"]);
					$arProductAditionalFields = explode(",", $productAditionalFields);
					if (isset($resultItem)) {
						foreach ($resultItem as $keyField => $valueField) {
							if (in_array($keyField, $arProductAditionalFields)) {
								$productItem->addAditionalField($keyField, $valueField);
							}
						}
					}
					$order->addItem(new Item($productItem, $resultItem["item_quantity"], ($resultItem["item_price"] * $result["currency_value"]), ($resultItem["total"] * $result["currency_value"])));
				}
			}
		}
		return json_encode(array("result" => "Ok", "data" => $order));
	}
}

function getProductCategoriesTree($productId) {
	$db_productCategories = sql_getProductCategories($productId);

	$listCategoriesTree = array();
	foreach ($db_productCategories as $result) {
		if ($result["parent_id"] > 0) {
			$tree = $result["name"];
			$parentId = $result["parent_id"];
			$idAnterior = $result["category_id"];
			while ($parentId > 0) {
				$db_category = sql_getCategory($parentId);
				$parentId = 0; // if empty return
				if (!empty($db_category[0])) {
					$resultCategory = $db_category[0];
					if ($idAnterior == $resultCategory["parent_id"]) {
						break;
					}

					$parentId = $resultCategory["parent_id"];
					$idAnterior = $resultCategory["category_id"];

					$tree = $resultCategory["name"] . " > " . $tree;
				}
			}
			$listCategoriesTree[] = html_entity_decode($tree);
		} else {
			$listCategoriesTree[] = html_entity_decode($result["name"]);
		}
	}

	return $listCategoriesTree;
}

function getProductImages($productId, $image = "") {
	$db_productImages = sql_getProductImages($productId);

	$images = array();

	if (is_file(DIR_IMAGE . $image)) {
		array_push($images, "image/" . $image);
	}

	foreach ($db_productImages as $image) {
		if (is_file(DIR_IMAGE . $image["image"])) {
			array_push($images, "image/" . $image["image"]);
		}
	}

	return $images;
}

function getProductSpecialPrice($productId) {
	$db_productSpecialPrices = sql_getProductSpecialPrices($productId);

	$dataAtual = date("Y-m-d");
	foreach ($db_productSpecialPrices as $result) {
		$dateStart = $result["date_start"] == "0000-00-00" ? date("Y-m-d", strtotime("-1 day")) : $result["date_start"];
		$dateEnd = $result["date_end"] == "0000-00-00" ? date("Y-m-d", strtotime("+1 day")) : $result["date_end"];

		if ($dateStart <= $dataAtual && $dataAtual < $dateEnd) {
			return $result["price"];
		}
	}

	return null;
}

function getProducts($user, $password, $version, $productCodeField, $aditionalFields, $page, $limit) {
	if (! (isset($productCodeField))) {
		$productCodeField = "M";
	}
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		$listProducts = array();
		$db_products = sql_getProducts();
		$initialRecordNumber = ($page - 1) * $limit;
		$finalRecordNumber = ($page * $limit) - 1;
		$recordNumber = 0;
		$aditionalFields = urldecode($aditionalFields);

		foreach ($db_products as $result) {
			if (($recordNumber >= $initialRecordNumber) && ($recordNumber <= $finalRecordNumber)) {
				$specialPrice = getProductSpecialPrice($result["product_id"]);
				$categoriesTree = getProductCategoriesTree($result["product_id"]);
				$model = $productCodeField == "S" ? $result["sku"] : $result["model"];
				$product = new Product($result["name"], $result["name"], $model, $result["product_id"], $result["price"], $result["quantity"], $result["weight"], $categoriesTree, $specialPrice, $result["height"], $result["width"], $result["length"]);
				$product->images = getProductImages($result["product_id"], $result["image"]);
				$arProductAditionalFields = explode(",", $aditionalFields);
				if (isset($result)) {
					foreach ($result as $keyField => $valueField) {
						if (in_array($keyField, $arProductAditionalFields)) {
							$product->addAditionalField($keyField, $valueField);
						}
					}
				}
				$listProducts[] = $product;
			}
			$recordNumber ++;
		}
		return json_encode(array("result" => "Ok", "data" => $listProducts));
	}
}

function getProduct($user, $password, $version, $productId, $productCodeField, $additionalFields) {
	if (!(isset($productCodeField))) {
		$productCodeField = "M";
	}

	if (!testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	}

	$db_product = sql_getProduct($productCodeField, $productId);
	$additionalFields = urldecode($additionalFields);
	$arProductAdditionalFields = explode(",", $additionalFields);

	foreach ($db_product as $result) {
		$specialPrice = getProductSpecialPrice($result["product_id"]);
		$categoriesTree = getProductCategoriesTree($result["product_id"]);
		$model = $productCodeField == "S" ? $result["sku"] : $result["model"];

		$product = new Product($result["name"], $result["name"], $model, $result["product_id"], $result["price"], $result["quantity"], $result["weight"], $categoriesTree, $specialPrice, $result["height"], $result["width"], $result["length"]);
		$product->images = getProductImages($result["product_id"], $result["image"]);

		if (isset($result)) {
			foreach ($result as $keyField => $valueField) {
				if (in_array($keyField, $arProductAdditionalFields)) {
					$product->addAditionalField($keyField, $valueField);
				}
			}
		}

		return json_encode(array("result" => "Ok", "data" => $product));
	}

	return json_encode(array("result" => "Error", "errorDetails" => "Produto não encontrado."));
}

$arProductOptions = array();
$productPrice = 0;
$productWeight = 0;

function getProductsAndOptions($user, $password, $version, $productCodeField, $aditionalFields, $page, $limit) {
	global $arProductOptions, $productPrice, $productWeight;
	
	if (! (isset($productCodeField))) {
		$productCodeField = "M";
	}
	
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		$listProducts = array();
		$db_products = sql_getProducts();
		$initialRecordNumber = ($page - 1) * $limit;
		$finalRecordNumber = ($page * $limit) - 1;
		$recordNumber = 0;
		
		$aditionalFields = urldecode($aditionalFields);
		
		foreach ($db_products as $result) {
			$categoriesTree = getProductCategoriesTree($result["product_id"]);
			$specialPrice = getProductSpecialPrice($result["product_id"]);
			$db_option = sql_getProductOptions($result["product_id"]);
			if (count($db_option) > 0) {
				$arOptions = array();
				foreach ($db_option as $resultOption) {
					$keyTmp = str_pad($resultOption["sort_order"], 10, "0", STR_PAD_LEFT) . str_pad($resultOption["option_id"], 10, "0", STR_PAD_LEFT);
					if (! (isset($arOptions[$keyTmp]))) {
						$arOptions[$keyTmp] = array();
					}
					$arOptions[$keyTmp][] = array("option_id" => $keyTmp, "option" => $resultOption["name"], "quantity" => $resultOption["quantity"], "price" => $resultOption["price"], "weight" => $resultOption["weight"]);
				}

				$arProductOptions = array();
				$productPrice = $result["price"];
				$productWeight = $result["weight"];
				getOptions(0, "", $arOptions);

				foreach ($arProductOptions as $optionValue) {
					if (($recordNumber >= $initialRecordNumber) && ($recordNumber <= $finalRecordNumber)) {
						$model = $productCodeField == "S" ? $result["sku"] : $result["model"];
						$product = new Product($result["name"], $result["name"] . " - " . $optionValue["name"], $model, $result["product_id"], $result["price"], $optionValue["quantity"], $result["weight"], $categoriesTree, $specialPrice);
						$product->images = getProductImages($result["product_id"], $result["image"]);
						$arProductAditionalFields = explode(",", $aditionalFields);
						if (isset($result)) {
							foreach ($result as $keyField => $valueField) {
								if (in_array($keyField, $arProductAditionalFields)) {
									$product->addAditionalField($keyField, $valueField);
								}
							}
						}
						$listProducts[] = $product;
					}
					$recordNumber ++;
				}
			} else {
				if (($recordNumber >= $initialRecordNumber) && ($recordNumber <= $finalRecordNumber)) {
					$model = $productCodeField == "S" ? $result["sku"] : $result["model"];
					$product = new Product($result["name"], $result["name"], $model, $result["product_id"], $result["price"], $result["quantity"], $result["weight"], $categoriesTree, $specialPrice);
					$product->images = getProductImages($result["product_id"], $result["image"]);
					$arProductAditionalFields = explode(",", $aditionalFields);
					if (isset($result)) {
						foreach ($result as $keyField => $valueField) {
							if (in_array($keyField, $arProductAditionalFields)) {
								$product->addAditionalField($keyField, $valueField);
							}
						}
					}
					$listProducts[] = $product;
				}
				$recordNumber ++;
			}
		}
		return json_encode(array("result" => "Ok", "data" => $listProducts));
	}
}

function updateShippingCode($user, $password, $version, $shippingData, $orderNumberField) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		if(isset($orderNumberField)) {
			$shippingData = json_decode($shippingData);
			$shippingData->orderId = $orderNumberField;
			
			sql_updateShippingCode($shippingData);

			return json_encode(array("result" => "Ok"));
		} else {
			return json_encode(array("result" => "Error", "errorDetails" => "Número do pedido não passado como parametro."));
		}
	}
}

function updateOrderStatus($user, $password, $version, $orderData) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		if(isset($orderData)) {
			$orderData = json_decode($orderData);

			if(isset($orderData->order_id)) {
				sql_updateOrderStatus($orderData);

				return json_encode(array("result" => "Ok"));
			} else {
				return json_encode(array("result" => "Error", "errorDetails" => "Número do pedido não passado como parametro."));
			}
		} else {
			return json_encode(array("result" => "Error", "errorDetails" => "Dados do pedido não passados como parametro."));
		}
	}
}

function insertUpdateProductSpecialPrice($productId, $specialPrice) {
	if ($specialPrice === null) {
		return;
	}

	sql_deleteProductSpecialPrices($productId);

	if ($specialPrice > 0) {
		sql_insertProductSpecialPrice($productId, $specialPrice);
	}
}

function insertUpdateProductCategory($productId, $categories) {
	if (empty($categories)) {
		return;
	}

	$categories = array_filter($categories, 'strlen');
	if (count($categories) > 0) {
		sql_deleteProductCategories($productId);
		$languageId = sql_getLanguageId();

		foreach ($categories as $categoryTree) {
			$aCategories = explode(" > ", $categoryTree);
			$aCategories = array_filter($aCategories, 'strlen');

			$parentId = 0;
			foreach ($aCategories as $name) {
				if (!empty($name)) {
					$data = array();
					$data["parent_id"] = $parentId;
					$data["category_description"] = array();
					$data["category_description"][$languageId] = array("name" => $name);

					$result = sql_getCategoryByName($name, $parentId);
					if (count($result) > 0) {
						$category = $result[0];
						$parentId = $category["category_id"];
					} else {
						$parentId = sql_addCategory($data);
					}
				}
			}

			sql_addProductCategory($productId, $parentId);
		}
	}
}

function insertUpdateProduct($user, $password, $version, $productData, $productCodeField) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		
		if (! (isset($productCodeField))) {
			$productCodeField = "M";
		}
		
		$productData = json_decode($productData);
		$productData->id = sql_getProductIdByCode($productData->id, $productData->model, $productCodeField);

		$data = array();
		$data["id"] = $productData->id;
		$data["model"] = $productData->model;
		$data["sku"] = $productData->model;
		$data["price"] = $productData->price;
		$data["weight"] = $productData->weight;
		$data["quantity"] = $productData->quantity;
		$data["ean"] = $productData->ean;
		$data["status"] = $productData->status;
		$data["stock_status_id"] = "5";
		$data["subtract"] = "1";

		if (isset($productData->height, $productData->width, $productData->length)) {
			$data["height"] = $productData->height;
			$data["width"] = $productData->width;
			$data["length"] = $productData->length;
		}

		$productData->description = decodeCharacters($productData->description);
		$productData->descriptionComplementar = decodeCharacters($productData->descriptionComplementar);

		$languageId = sql_getLanguageId();
		$data["product_description"] = array();
		$data["product_description"][$languageId] = array("name" => $productData->description, "description" => $productData->descriptionComplementar);
		
		$data["image"] = null;
		$data["product_image"] = array();
		
		if (!empty($productData->images)) {
			foreach ($productData->images as $value) {
				$path = file_get_contents(urldecode($value->url));
				if ($version == "2.0.0") {
					$fp = fopen("../image/catalog/" . $value->name, "w");
				} else {
					$fp = fopen("../image/data/" . $value->name, "w");
				}
				fwrite($fp, $path);
				fclose($fp);

				if ($data["image"] == null) {
					if ($version == "2.0.0") {
						$data["image"] = "catalog/" . $value->name;
					} else {
						$data["image"] = "data/" . $value->name;
					}
				} else {
					if ($version == "2.0.0") {
						$data["product_image"][] = array("image" => "catalog/" . $value->name);
					} else {
						$data["product_image"][] = array("image" => "data/" . $value->name);
					}
				}
			}
		}

		sql_insertUpdateProduct($data, $version);

		if (!empty($productData->categories)) {
			insertUpdateProductCategory($productData->id, decodeCharacters($productData->categories));
		}

		if (isset($productData->special_price)) {
			insertUpdateProductSpecialPrice($productData->id, $productData->special_price);
		}

		array_unshift($data["product_image"], ["image" => $data["image"]]);

		return json_encode(array("result" => "Ok", "images" => $data["product_image"]));
	}
}

function setProductStockQuantity($user, $password, $version, $productData, $productCodeField) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		
		if (! (isset($productCodeField))) {
			$productCodeField = "M";
		}
		
		$productData = json_decode($productData);
		$productData->id = sql_getProductIdByCode($productData->id, $productData->model, $productCodeField);

		$data = array();
		$data["id"] = $productData->id;
		$data["quantity"] = $productData->quantity;
		
		sql_setProductStockQuantity($data);
		
		return json_encode(array("result" => "Ok"));
	}
}

function setProductPrice($user, $password, $version, $productData, $productCodeField) {
	if (! testUser($user, $password, $version)) {
		return json_encode(array("result" => "Error", "errorDetails" => "Usuário não cadastrado ou senha incorreta."));
	} else {
		if (! (isset($productCodeField))) {
			$productCodeField = "M";
		}

		$productData = json_decode($productData);
		$productData->id = sql_getProductIdByCode($productData->id, $productData->model, $productCodeField);

		$data = array();
		$data["id"] = $productData->id;
		$data["price"] = $productData->price;

		sql_setProductPrice($data);

		if (isset($productData->special_price)) {
			insertUpdateProductSpecialPrice($productData->id, $productData->special_price);
		}

		return json_encode(array("result" => "Ok"));
	}
}

// === Auxiliary methods ============================================================================
function testUser($user, $password, $version) {
	$db_user = sql_getUser($user, $password, $version);
	foreach ($db_user as $result) {
		return true;
	}
	return false;
}

function getProductNameAndOptions($order_product_id) {
	$db_options = sql_getOrderProductOptions($order_product_id);
	$result = "";
	foreach ($db_options as $resultOption) {
		$result .= " - " . $resultOption["value"];
	}
	return $result;
}

function getOptions($option_id, $descricao, $arOptions) {
	global $arProductOptions, $productPrice, $productWeight;
	
	foreach ($arOptions as $keyOption => $valueoption) {
		if ($keyOption > $option_id) {
			foreach ($arOptions[$keyOption] as $keyAux => $valueAux) {
				if (trim($descricao) == "") {
					$descricaoAux = getOptions($keyOption, $valueAux["option"], $arOptions);
				} else {
					$descricaoAux = getOptions($keyOption, $descricao . " |-| " . $valueAux["option"], $arOptions);
				}
				
				$test = explode("|-|", $descricaoAux);
				
				if (count($test) == count($arOptions)) {
					$productName = str_replace("|-|", "-", $descricaoAux);
					$arProductOptions[] = array("name" => $productName, "price" => ($productPrice + $valueAux["price"]), "quantity" => $valueAux["quantity"], "weight" => ($productWeight + $valueAux["weight"]));
				}
			}
		}
	}
	return $descricao;
}

function decodeCharacters($text) {
	$caracteres = [
		"#amp;" => "&",
		"#lt;" => "<",
		"#gt;" => ">",
		"#quot;" => "'",
	];

	$search = array_keys($caracteres);
	$replace = array_values($caracteres);

	return str_replace($search, $replace, $text);
}
