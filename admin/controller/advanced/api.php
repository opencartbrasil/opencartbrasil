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

		$data['api_keys'] = array(
			array(
				'api_key_id' => 1,
				'description' => 'Descrição da API Rest',
				'status' => true,
				'date_added' => '17/07/2021',
				'date_modified' => '18/07/2021',
				'last_access' => '19/07/2021',
				'edit' => $this->url->link('advanced/api/edit', $url . '&api_key_id=' . 1, true),
				'logs' => $this->url->link('advanced/api/edit', $url . '&api_key_id=' . 1 . '#access', true),
				'disable' => $this->url->link('advanced/api/disable', $url . '&api_key_id=' . 1, true),
			)
		);

		$data['sort'] = $sort;
		$data['order'] = $order;

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

		//$this->load->model('advanced/api');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			//$this->model_advanced_api->addApi($this->request->post);

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

		//$this->load->model('advanced/api');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			//$this->model_advanced_api->editApi($this->request->get['api_key_id'], $this->request->post);

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

		//$this->load->model('advanced/api');

		if (isset($this->request->post['selected']) && $this->validateModify()) {
			foreach ($this->request->post['selected'] as $api_id) {
				//$this->model_advanced_api->deleteApi($api_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->index();
	}

	public function disable() {
		$this->load->language('advanced/api');

		$this->document->setTitle($this->language->get('heading_title'));

		//$this->load->model('advanced/api');

		if (isset($this->request->get['api_key_id']) && $this->validateModify()) {
			//$this->model_advanced_api->disableApi($this->request->post['api_key_id']);

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->index();
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

		$data['history'] = array(
			array(
				'api_key_id' => 1,
				'ip_address' => '127.0.0.1',
				'date_added' => '19/07/2021 15:02:00'
			),
			array(
				'api_key_id' => 2,
				'ip_address' => '0.0.0.0',
				'date_added' => '19/07/2021 16:02:00'
			),
			array(
				'api_key_id' => 3,
				'ip_address' => '192.168.1.100',
				'date_added' => '19/07/2021 17:02:00'
			),
		);

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

		return !$this->error;
	}

	protected function validateModify() {
		if (!$this->user->hasPermission('modify', 'advanced/api')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
