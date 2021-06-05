<?php
class ControllerInstallStep1 extends Controller {
	public function index() {
		$data = $this->load->language('install/step_1');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->response->redirect($this->url->link('install/step_2'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['action'] = $this->url->link('install/step_1');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('install/step_1', $data));
	}
}
