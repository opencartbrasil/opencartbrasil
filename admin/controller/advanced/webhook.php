<?php

class ControllerAdvancedWebHook extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('advanced/webhook');

		$this->document->setTitle($this->language->get('heading_title'));

		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = false;
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

		$this->load->model('advanced/webhook');

		$hooks = $this->model_advanced_webhook->getHooks($filter_data);

		$hook_total = $this->model_advanced_webhook->getHooksTotal();

		$data['hooks'] = array();

		foreach ($hooks as $hook) {
			$data['hooks'][] = array(
				'webhook_client_id' => $hook['webhook_client_id'],
				'description' => $hook['description'],
				'status' => !!$hook['status'],
				'actions_hook' => explode(',', $hook['actions']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($hook['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($hook['date_modified'])),
				'edit' => $this->url->link('advanced/webhook/edit', $url . '&webhook_client_id=' . $hook['webhook_client_id'], true),
				'logs' => $this->url->link('advanced/webhook/edit', $url . '&webhook_client_id=' . $hook['webhook_client_id'] . '#access', true),
				'toggleStatus' => $this->url->link('advanced/webhook/toggleStatus', $url . '&webhook_client_id=' . $hook['webhook_client_id'], true),
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
		$pagination->total = $hook_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf(
			$this->language->get('text_pagination'),
			($hook_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
			((($page - 1) * $this->config->get('config_limit_admin')) > ($hook_total - $this->config->get('config_limit_admin')))
				? $hook_total
				: ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
			$hook_total, ceil($hook_total / $this->config->get('config_limit_admin'))
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

		$data['sort_description'] = $this->url->link('advanced/webhook', $url . '&sort=description', true);
		$data['sort_status'] = $this->url->link('advanced/webhook', $url . '&sort=status', true);
		$data['sort_date_added'] = $this->url->link('advanced/webhook', $url . '&sort=date_added', true);
		$data['sort_date_modified'] = $this->url->link('advanced/webhook', $url . '&sort=date_modified', true);
		$data['sort_actions_hook'] = $this->url->link('advanced/webhook', $url . '&sort=actions', true);

		$data['add'] = $this->url->link('advanced/webhook/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('advanced/webhook/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('advanced/webhook_list', $data));
	}

	public function add() {
		$this->load->language('advanced/webhook');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('advanced/webhook');

		$this->getForm();
	}

	public function edit() {
		$this->load->language('advanced/webhook');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('advanced/webhook');

		$this->getForm();
	}

	public function getForm() {
		$this->load->language('advanced/webhook');

		$this->document->setTitle($this->language->get('heading_title'));

		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = false;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('advanced/webhook_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'advanced/webhook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateModify() {
		if (!$this->user->hasPermission('modify', 'advanced/webhook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
