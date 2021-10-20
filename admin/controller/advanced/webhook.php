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

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = false;
		}

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
				'actions_hook' => $hook['actions'],
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

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_advanced_webhook->addHook($this->request->post);

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

			$this->response->redirect($this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('advanced/webhook');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('advanced/webhook');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_advanced_webhook->editHook($this->request->get['webhook_client_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('advanced/webhook');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('advanced/webhook');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateModify()) {
			foreach ($this->request->post['selected'] as $selected) {
				$this->model_advanced_webhook->deleteHook($selected);
			}

			$this->session->data['success'] = $this->language->get('text_deleted');

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

			$this->response->redirect($this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->index();
	}

	public function request_info() {
		$webhook_request_history_id = $this->request->get['webhook_request_history_id'];

		$this->load->model('advanced/webhook');

		$data = $this->model_advanced_webhook->getRequestHistoryInfo($webhook_request_history_id);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	public function toggleStatus() {
		$this->load->language('advanced/webhook');

		if (isset($this->request->get['webhook_client_id']) && $this->validateModify()) {
			$this->load->model('advanced/webhook');

			$this->model_advanced_webhook->toggleHook($this->request->get['webhook_client_id']);

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

		$this->response->redirect($this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	protected function getForm() {
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

		if (isset($this->error['url'])) {
			$data['error_url'] = $this->error['url'];
		} else {
			$data['error_url'] = false;
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = false;
		}

		$webhook_client_info = array();

		if (isset($this->request->get['webhook_client_id'])) {
			$webhook_client_info = $this->model_advanced_webhook->getHook($this->request->get['webhook_client_id']);
		} else {
			$webhook_client_info = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (isset($webhook_client_info['status'])) {
			$data['status'] = $webhook_client_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (isset($webhook_client_info['description'])) {
			$data['description'] = $webhook_client_info['description'];
		} else {
			$data['description'] = '';
		}

		if (isset($this->request->post['url'])) {
			$data['url'] = $this->request->post['url'];
		} elseif (isset($webhook_client_info['url'])) {
			$data['url'] = $webhook_client_info['url'];
		} else {
			$data['url'] = '';
		}

		if (isset($this->request->post['auth_user'])) {
			$data['auth_user'] = $this->request->post['auth_user'];
		} elseif (isset($webhook_client_info['auth_user'])) {
			$data['auth_user'] = $webhook_client_info['auth_user'];
		} else {
			$data['auth_user'] = '';
		}

		if (isset($this->request->post['auth_password'])) {
			$data['auth_password'] = $this->request->post['auth_password'];
		} elseif (isset($webhook_client_info['auth_password'])) {
			$data['auth_password'] = $webhook_client_info['auth_password'];
		} else {
			$data['auth_password'] = '';
		}

		if (isset($this->request->post['headers'])) {
			$data['headers'] = $this->request->post['headers'];
		} elseif (isset($webhook_client_info['headers'])) {
			$data['headers'] = $webhook_client_info['headers'];
		} else {
			$data['headers'] = [];
		}

		if (isset($this->request->post['actions'])) {
			$data['actions'] = $this->request->post['actions'];
		} elseif (isset($webhook_client_info['actions'])) {
			$data['actions'] = $webhook_client_info['actions'];
		} else {
			$data['actions'] = [];
		}

		if (isset($this->request->get['webhook_client_id'])) {
			$data['action'] = $this->url->link('advanced/webhook/edit', 'user_token=' . $this->session->data['user_token'] . '&webhook_client_id=' . $this->request->get['webhook_client_id'], true);
			$url = 'user_token=' . $this->session->data['user_token'] . '&webhook_client_id=' . $this->request->get['webhook_client_id'];
		} else {
			$data['action'] = $this->url->link('advanced/webhook/add', 'user_token=' . $this->session->data['user_token'], true);
			$url = 'user_token=' . $this->session->data['user_token'];
		}

		$data['cancel'] = $this->url->link('advanced/webhook', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$history = array();
		$history_total = 0;

		if (isset($this->request->get['webhook_client_id'])) {
			$history = $this->model_advanced_webhook->getRequestHistory($this->request->get['webhook_client_id'], $filter_data);

			$history_total = $this->model_advanced_webhook->getRequestHistoryTotal($this->request->get['webhook_client_id']);
		}

		$data['history'] = array();

		foreach ($history as $value) {
			$data['history'][] = array(
				'webhook_request_history_id' => $value['webhook_request_history_id'],
				'action' => $value['action'],
				'status_code' => $value['status_code'],
				'status_ok' => ($value['status_code'] >= 200 && $value['status_code'] < 300),
				'date_added' => date($this->language->get('datetime_format'), strtotime($value['date_added'])),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('advanced/webhook/edit', $url . '&page={page}#access', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf(
			$this->language->get('text_pagination'),
			($history_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
			((($page - 1) * $this->config->get('config_limit_admin')) > ($history_total - $this->config->get('config_limit_admin')))
				? $history_total
				: ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
			$history_total, ceil($history_total / $this->config->get('config_limit_admin'))
		);

		$data['webhook_request_history_info'] = $this->url->link('advanced/webhook/request_info', 'user_token=' . $this->request->get['user_token'], true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('advanced/webhook_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'advanced/webhook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!filter_var($this->request->post['url'], FILTER_VALIDATE_URL)) {
			$this->error['url'] = $this->language->get('error_url');
		}

		if (mb_strlen(trim($this->request->post['description'])) < 3 || mb_strlen(trim($this->request->post['description'])) > 255) {
			$this->error['description'] = $this->language->get('error_description');
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
