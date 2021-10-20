<?php
class ControllerAdvancedApi extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('advanced/api');

		$this->document->setTitle($this->language->get('heading_title'));

		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('advanced/api', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = false;
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = false;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'description';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = 'user_token=' . $this->session->data['user_token'];

		if ($order == 'DESC') {
			$url .= '&order=DESC';
		}

		if ($sort) {
			$url .= '&sort=' . $sort;
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$filter_data = array(
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$this->load->model('advanced/api');

		$api_keys = $this->model_advanced_api->getApis($filter_data);

		$api_keys_total = $this->model_advanced_api->getApisTotal();

		$data['api_keys'] = array();

		foreach ($api_keys as $api_key) {
			$data['api_keys'][] = array(
				'api_key_id' => $api_key['api_key_id'],
				'description' => $api_key['description'],
				'status' => !!$api_key['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($api_key['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($api_key['date_modified'])),
				'last_access' => $api_key['last_access'] ? date($this->language->get('datetime_format'), strtotime($api_key['last_access'])) : '-',
				'edit' => $this->url->link('advanced/api/edit', $url . '&api_key_id=' . $api_key['api_key_id'], true),
				'logs' => $this->url->link('advanced/api/edit', $url . '&api_key_id=' . $api_key['api_key_id'] . '#access', true),
				'toggleStatus' => $this->url->link('advanced/api/toggleStatus', $url . '&api_key_id=' . $api_key['api_key_id'], true),
			);
		}

		$data['sort'] = $sort;
		$data['order'] = $order;

		$url = '';

		if ($order == 'DESC') {
			$url .= '&order=DESC';
		}

		if ($sort) {
			$url .= '&sort=' . $sort;
		}

		$pagination = new Pagination();
		$pagination->total = $api_keys_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('advanced/api', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf(
			$this->language->get('text_pagination'),
			($api_keys_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
			((($page - 1) * $this->config->get('config_limit_admin')) > ($api_keys_total - $this->config->get('config_limit_admin')))
				? $api_keys_total
				: ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
			$api_keys_total, ceil($api_keys_total / $this->config->get('config_limit_admin'))
		);

		$url = 'user_token=' . $this->session->data['user_token'];

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_description'] = $this->url->link('advanced/api', $url . '&sort=description', true);
		$data['sort_status'] = $this->url->link('advanced/api', $url . '&sort=status', true);
		$data['sort_date_added'] = $this->url->link('advanced/api', $url . '&sort=date_added', true);
		$data['sort_date_modified'] = $this->url->link('advanced/api', $url . '&sort=date_modified', true);
		$data['sort_last_access'] = $this->url->link('advanced/api', $url . '&sort=last_access', true);

		$data['add'] = $this->url->link('advanced/api/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('advanced/api/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('advanced/api_list', $data));
	}

	public function add() {
		$this->load->language('advanced/api');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('advanced/api');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_advanced_api->addApi($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('advanced/api', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('advanced/api');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('advanced/api');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_advanced_api->editApi($this->request->get['api_key_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('advanced/api', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('advanced/api');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('advanced/api');

		if (isset($this->request->post['selected']) && $this->validateModify()) {
			foreach ($this->request->post['selected'] as $api_id) {
				$this->model_advanced_api->deleteApi($api_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->response->redirect($this->url->link('advanced/api', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function toggleStatus() {
		$this->load->language('advanced/api');

		if (isset($this->request->get['api_key_id']) && $this->validateModify()) {
			$this->load->model('advanced/api');

			$this->model_advanced_api->toggleStatusApi($this->request->get['api_key_id']);

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->response->redirect($this->url->link('advanced/api', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function getForm() {
		$this->load->language('advanced/api');

		$this->document->setTitle($this->language->get('heading_title'));

		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('advanced/api', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = false;
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}

		if (isset($this->error['consumer_key'])) {
			$data['error_consumer_key'] = $this->error['consumer_key'];
		} else {
			$data['error_consumer_key'] = '';
		}

		if (isset($this->error['consumer_secret'])) {
			$data['error_consumer_secret'] = $this->error['consumer_secret'];
		} else {
			$data['error_consumer_secret'] = '';
		}

		$this->load->model('advanced/api');

		if (isset($this->request->get['api_key_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$api_info = $this->model_advanced_api->getApi($this->request->get['api_key_id']);
		} else {
			$api_info = array();
		}

		if (isset($api_info['description'])) {
			$data['description'] = $api_info['description'];
		} elseif (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($api_info['consumer_key'])) {
			$data['consumer_key'] = $api_info['consumer_key'];
		} elseif (isset($this->request->post['consumer_key'])) {
			$data['consumer_key'] = $this->request->post['consumer_key'];
		} else {
			$data['consumer_key'] = 'ck_' . bin2hex(random_bytes(32));
		}

		if (isset($api_info['consumer_secret'])) {
			$data['consumer_secret'] = $api_info['consumer_secret'];
		} elseif (isset($this->request->post['consumer_secret'])) {
			$data['consumer_secret'] = $this->request->post['consumer_secret'];
		} else {
			$data['consumer_secret'] = 'cs_' . bin2hex(random_bytes(32));
		}

		if (isset($api_info['permissions'])) {
			$data['permissions'] = explode(',', $api_info['permissions']);
		} elseif (isset($this->request->post['permissions'])) {
			$data['permissions'] = $this->request->post['permissions'];
		} else {
			$data['permissions'] = '';
		}

		if (isset($api_info['status'])) {
			$data['status'] = $api_info['status'];
		} elseif (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->get['api_key_id'])) {
			$data['api_key_id'] = (int)$this->request->get['api_key_id'];
			$histories = $this->model_advanced_api->getApiHistories($this->request->get['api_key_id']);
		} else {
			$data['api_key_id'] = false;
			$histories = array();
		}

		$data['history'] = [];

		foreach ($histories as $history) {
			$data['history'][] = array(
				'api_history_id' => $history['api_history_id'],
				'type' => $this->language->get('text_type_' . $history['type']),
				'ip_address' => $history['ip_address'],
				'date_added' => date($this->language->get('datetime_format'), strtotime($history['date_added']))
			);
		}

		$url = 'user_token=' . $this->session->data['user_token'];

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['api_key_id'])) {
			$data['action'] = $this->url->link('advanced/api/edit', $url . '&api_key_id=' . $this->request->get['api_key_id'], true);
		} else {
			$data['action'] = $this->url->link('advanced/api/add', $url, true);
		}

		$data['cancel'] = $this->url->link('advanced/api', $url, true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('advanced/api_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'advanced/api')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen(trim($this->request->post['description'])) < 3) || (utf8_strlen(trim($this->request->post['description'])) > 255)) {
			$this->error['description'] = $this->language->get('error_description');
		}

		if (!isset($this->request->get['api_key_id'])) {
			if ((utf8_strlen(trim($this->request->post['consumer_key'])) < 7) || (utf8_strlen(trim($this->request->post['consumer_key'])) > 67) || strpos($this->request->post['consumer_key'], 'ck_') !== 0) {
				$this->error['consumer_key'] = $this->language->get('error_consumer_key');
			}

			if ((utf8_strlen(trim($this->request->post['consumer_secret'])) < 7) || (utf8_strlen(trim($this->request->post['consumer_secret'])) > 67) || strpos($this->request->post['consumer_secret'], 'cs_') !== 0) {
				$this->error['consumer_secret'] = $this->language->get('error_consumer_secret');
			}

			$consumer_key_exists = $this->model_advanced_api->consumerKeysExists($this->request->post);

			if ($consumer_key_exists && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_consumer_exist');
			}
		}

		return !$this->error;
	}

	protected function validateModify() {
		if (!$this->user->hasPermission('modify', 'advanced/api')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
